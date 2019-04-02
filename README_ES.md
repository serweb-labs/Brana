# Brana Headless CMS

Brana is a headless content manager framework write in moderm PHP (Hello  Symfony 4!).

Currently in the design phase, but now you can take a look at the API.


Create your API or webserservice quickly without losing flexibility.


# Store layer API
Los campos y sus tipos (es decir el esquema de datos) se definen cargando un archivo yaml en /config/contenttypes/, de hecho normalmente usted solo tendra que agregar su esquema en /config/contenttypes/app.yml y Brana se encargara del trabajo pesado.

```yaml
# /config/contenttypes/app.yml
book:
  name: book
  plural_name: books
  pk: id
  fields:
    id:
      type: integer
    title:
      type: text
      default: untitled
    author:
      type: text
    isbn:
      type: text
    is_bestseller:
      type: boolean
      default: false
    status: 
      type: select
      values: [published, draf]
      default: published

```
## Perfect!, ready to use the Store layer

```php
// get the page manager
$pageManager = $store->getManager('page')

// create a page and set title
$page = $pageManager->create();
$page->title  = 'good news!';
$pageManager->save($page);

// get the page with id 1 and destroy
$page = $pageManager->get(['id' => 1]);
$pageManager->delete($page);

// get all pages with title 'untitled' and
// mark as draft
$pages = $pageManager->filter(['title' => 'untitled']);

foreach ($pages as $page) {
    $page->status = 'published';
    $pageManager->save($page);
}

```
Note that $store is a service, instance of /Brana/Store/Store 
you can use the store service in any other service or controller.

# Hacking the Store
En Brana, cada tipo de contenido tiene asociado un esquema de datos (el archivo yaml en /config/contenttypes/), una clase de entidad
y un manager, mientras la clase de entidad defina las reglas del juego para interactuar con una una instancia de ese tipo de contenido, el manager se encarga de la recuperacion, persistencia, creacion y eliminacion de sus registros (similar al concepto "repositorio" en el patrón homonimo).

Una vez que usted difinio su esquema en el archivo yaml, Brana le asignará bajo el capó una clase de entidad y un manager generico, probablemente esto sea suficiente para usted, sin embargo en el mundo real usted deseará hacer cosas durante el guardado de la instancia o lidear con logica de negocios compleja, en esos casos probablemente usted quiera escribir un manager propio y quiza una clase de entidad para limitar la interaccion y asegurar la consistencia de sus datos.



## For example

```php
// /src/App/Store.php

$store->setEntity('page', /App/Store/Entity/PageEntity::class);
$store->setManager('page', /App/Store/Manager/PageManager::class);

```

## Equivalent to
```php
// /src/App/Store.php

$store->set('page', [
    'entity'=>/App/Store/Entity/PageEntity::class,
    'manager'=>/App/Store/Manager/PageManager::class,
]);

```


# filtering with Query API

Ademas de un array, el method filter del manager generico de Brana puede recibir queries mas o menos complejas.

## Simple query
```php
use Brana\Store\Query;

$qs = Query::qs()
    ->where('author', '=', 'jorge luis borges')
    ->or()
    ->where('author', '=', 'julio cortazar')
    ->limit(1)
    ->orderBy("title");

$books = $bookManager->filter($qs);

// equivalent to
$books = $bookManager->query()
->where('author', '=', 'jorge luis borges')
->or()
->where('author', '=', 'julio cortazar')
->limit(1)
->orderBy("title")
->execute()

```
## More complex query

```php
use Brana\Store\Query;

$qs = Query::qs()
->where('rating', '>', 3)
->and(
    Query::qs()
    ->where('creation_date', 'between', '18/03/2018:18/03/2019')
    ->or()
    ->where('creation_date', 'between', '18/03/2015:18/03/2016')
)
->and(
    Query::qs()
    ->where('author', '=', 'jorge luis borges')
    ->or()
    ->where('author', '=', 'julio cortazar')
)

$bookManager->query()
->setQuery($qs)
->limit(1)
->orderBy("title")
->execute();

// equivalent to
$books = $bookManager->filter(
    $qs->limit(1)->orderBy("title")
)

```
Si no desea usar el manager, puede usar directamente 
el servicio de query para decirle que tipo de contenido debe recuperar, internamente usara el manager del mismo para instanciar los objetos.

```php
use Brana\Store\Query;

$pages = Query::qs()
->contentType('books')
->where('rating', '>', 3)
->execute();


```
Esto se debería evitar en la mayoria de los casos
pero puede ser sumamente util si no sabemos que tipo
de contenido tenemos que recuperar, como cuando cargamos una consulta desde un JSON o un array como veremos a continuacion.


## Load query from JSON
```json
{
  "type": "query",
  "entity": "pages",
  "members": [
    {
      "type": "where",
      "nexo": "and",
      "expr": [
          "rating",
          ">",
          3
        ]
    },
    {
      "type": "member",
      "nexo": "and",
      "members": [
        {
          "type": "where",
          "nexo": "and",
          "expr": [
            "creation_date",
            "between",
            "18/03/2018:18/03/2019"
          ]
        },
        {
          "type": "where",
          "nexo": "or",
          "expr": [
            "creation_date",
            "between",
            "18/03/2014:18/03/2015"
          ]
        }
      ]
    },
    {
      "type": "member",
      "nexo": "or",
      "members": [
        {
          "type": "where",
          "nexo": "and",
          "expr": [
            "author",
            "=",
            "jorge luis borges"
          ]
        },
        {
          "type": "where",
          "nexo": "or",
          "expr": [
            "author",
            "=",
            "julio cortazar"
          ]
        }
      ]
    },
    {
      "type": "limit",
      "value": 1
    },
    {
      "type": "orderBy",
      "value": "title"
    }
  ]
}
```


```php
// controller handler for /api/easyQuery

use Brana\Store\Query;
use Symfony\Component\HttpFoundation\JsonResponse;

public function query(Query $query)
{
    $jsonQuery = file_get_contents('php://input');
    $results = $query::qs()
    ->fromJson($jsonQuery)
    ->execute();
    
    return new JsonResponse (
        $this->collectionToArray($results)
    )
}
```

## Load query from PHP array 
```php
// other controller

use Brana\Store\Query;
use Symfony\Component\HttpFoundation\JsonResponse;

public function _construct(Query $query)
{
    $this->query = $query;
}

public function query($contenttype)
{
    $arrayQuery = array (
    'type' => 'query',
    'entity' => $contenttype,
    'members' => array (
        array (
        'type' => 'where',
        'nexo' => 'and',
        'expr' => array (
            'rating',
            '>',
            3,
        ),
        ),
        array (
        'type' => 'member',
        'nexo' => 'and',
        'members' => array (
            array (
            'type' => 'where',
            'nexo' => 'and',
            'expr' => array (
                'creation_date',
                'between',
                '18/03/2018:18/03/2019',
            ),
            ),
            array (
            'type' => 'where',
            'nexo' => 'or',
            'expr' => array (
            'creation_date',
                'between',
                '18/03/2014:18/03/2015',
            ),
            ),
        ),
        ),
        array (
        'type' => 'member',
        'nexo' => 'or',
        'members' => array (
            array (
            'type' => 'where',
            'nexo' => 'and',
            'expr' => array (
                'author',
                '=',
                'jorge luis borges',
            ),
            ),
            array (
            'type' => 'where',
            'nexo' => 'or',
            'expr' => array (
                'author',
                '=',
                'julio cortazar',
            ),
            ),
        ),
        ),
        array (
        'type' => 'limit',
        'value' => 1,
        ),
        array (
        'type' => 'orderBy',
        'value' => 'title',
        ),
    ),
    );

    $results = $query::qs()
    ->fromArray($arrayQuery)
    ->execute();

    return new JsonResponse (
        $this->collectionToArray($results)
    )
}
```

# Rest API Framework
Brana viene con un rest API configurado, que sigue la especificacion JSON API.

Por defecto servira todos los campos de todos los tipos de contenido, pero claro que este
puede ser anulado.

## Routing

```yaml
// routes.yaml (brana core)

####
# CUSTOMIZATIONS AND OVERWRITES
####

# overwrite the controller
# for retrieve a page
rest_api_pages_retrieve:
    path: /pages/{slug}
    prefix: /api/1.0
    controller: Custom\Rest\PagesRestController::retrieve

####
# BUNDLES AND CORE
####

# import routes of JsonApi bundle
# esto debe estar abajo de las
# personalizaciones 
rest_api:
    path: /{contenttype}
    resource: Brana\JsonApi
    type: bundle
    prefix: /api/1.0

```

```yaml
// routes.yaml (rest bundle)

contenttype_list:
    path: /{contenttype}
    controller: Brana\Controller\ContentRestController::list
    methods: GET

contenttype_retrieve:
    path: /{contenttype}/{slug}
    controller: Brana\Controller\ContentRestController::retrieve
    methods: GET

contenttype_update:
    path: /{contenttype}/{slug}
    controller: Brana\Controller\ContentRestController::update
    methods: PUT

contenttype_create:
    path: /{contenttype}/{slug}
    controller: Brana\Controller\ContentRestController::create
    methods: POST

contenttype_partial_update:
    path: /{contenttype}/{slug}
    controller: Brana\Controller\ContentRestController::update
    methods: PATCH

contenttype_destroy:
    path: /{contenttype}/{slug}
    controller: Brana\Controller\ContentRestController::destroy
    methods: DELETE
```


## Controller
```php
namespace \Brana\Store\Serializers\ContentRestController;

// perform and filtering
public function listQueryset();
public function filterQueryset();
public function getObject();
public function getSerializer();
public function getPK();
public function createObject();
public function updateObject();
public function deleteObject();
public function getFields();
public function render();

// entry points
public function update();
public function partialUpdate();
public function create();
public function destroy();
public function list();
public function retrieve();

```
___
## Serializer: ContentSerializer
```php
namespace \Brana\Store\Serializers\ContentSerializer;

public function getFields();
public function validateData();
public function performSerialization();
public function isValid();
public function create();
public function update();

```
___
## Hacking and extend ContentSerializer
```yaml
// config/content_serializer.yml
// this configuration is used by
// \Brana\Store\Serializers\ContentSerializer
// and the serializers that extend from this
pages:
  contenttype: pages
  serializer: \Custom\Serializers\Pages
  fields_mode: '::getAllFields'
  create: '::create'
  fields:
    title:
      validate: /[^0-9]/
      transform: '::upperCase'
      readonly: true
      writeonly: false
    owner:
      readonly: true
      writeonly: false

```

