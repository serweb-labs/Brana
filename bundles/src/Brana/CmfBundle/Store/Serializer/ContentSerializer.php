<?php
namespace Brana\CmfBundle\Store\Serializer;

use Brana\CmfBundle\Store\Entity\BranaEntityInterface as BranaEntity;
use Brana\CmfBundle\Store\Manager\ManagerInterface as BranaManager;

class ContentSerializer // implements BranaSerializerInterface
{
    private $fieldMapping;
    private $params;

    public function __construct(BranaManager $manager, array $context) {
        $this->manager = $manager;
        $this->context = $context;
        $this->params = $context['params'];
        $this->request = $context['request'];
        $this->fieldMapping = [
            'integer' => Field\IntegerSerializer::class,
            'text' => Field\TextSerializer::class,
            'date' => Field\DateSerializer::class,
            'slug' => Field\TextSerializer::class,
        ];
    }


    public function getAllFields()
    {
        $fields = [];
        $ctName = $this->manager->contenttype['name'];
        foreach ($this->manager->contenttype['fields'] as $key => $value) {
            $fieldSerializer = $this->fieldMapping[$value['type']];
            $fields[$key] = [
                'serializer' => $fieldSerializer,
                'required' => false,
                'read_only' => false,
                'write_only' => false,
                'match' => false,
            ];
            if (isset($params[$ctName]['fields'][$key])) {
                $fields[$key] = array_merge($fields[$key], $params[$ctName]['fields'][$key]);
            }
        }
        return $fields;
    }


    public function getFields($keys)
    {
        $fields = [];
        $ctName = $this->manager->contenttype['name'];
        foreach ($keys as $ct) {
            $type = $this->manager->contenttype['fields'][$ct]['type'];
            $fieldSerializer = $this->fieldMapping[$type];
            $fields[$key] = [
                'serializer' => $fieldSerializer,
                'required' => false,
                'read_only' => false,
                'write_only' => false,
                'match' => false,
            ];
            if (isset($params[$ctName]['fields'][$key])) {
                $fields[$key] = array_merge($fields[$key], $params[$ctName]['fields'][$key]);
            }
        }
        return $fields;
    }


    public function setFields($fields)
    {
        return $fields;
    }


    public function setValues($values)
    {
        return $values;
    }


    public function transform(array $data, array $fields)
    {   
        foreach ($fields as $key => $value) {
            $data[$key] = $this->resolveAndCall($value['transform'], $data[$key]);
        }
        return $data;
    }

    // TODO
    // move to static method in cmf namespace
    // code CustomFunc constraint 
    public function validateField($field, $value, $constraints)
    {   
        $feedback = [];
        if (!is_array($constraints)) {
            $constraints = [$constraints];
        }

        $validator = Validation::createValidator();
        $checks = [];
        foreach ($constraints as $key => $value) {
            if (is_string($key) && strpos($key, "::") !== false) {
                $checks[] = new Assert\CustomFunc(["func"=> $value]);
            }
            else {
                $checks[] = new $key($value);
            }
        }

        $violations = $validator->validate($value, $checks);

        if (0 !== count($violations)) {
            foreach ($violations as $violation) {
                $feedback[] = $violation->getMessage();
            }
        }

        return $feedback;
    }

    public function validator(array $data, $fields, $early = false)
    {
        $errors = [];

        foreach ($fields as $key => $value) {
            $errors[$key] = [];
            if ($value['required'] && !isset($data[$key])) {
                $errors[$key][] = 'is required';
                if ($early) {
                    return $errors;
                }
            }
            if ($value['constraints']) {
                $constraintsErrors = $this->validateField($value['constraints'], $key, $data[$key]);
                $errors[$key] = array_merge($errors[$key], $constraintsErrors);
                if ($early && 0 !== count($errors[$key])) {
                    return $errors;
                }
            }
        }
        return $errors;
    }


    public function create($request)
    {
        $props = [];
        $fields = $this->getAllFields();
        $fields = $this->setFields($fields);
        $values = $this->getValuesByRequest();
        $errors = $this->validator($rawProps, $fields, false); // early from yaml
        $data = null;
        if (0 === count($errors)) {
            $values = $this->setValues($values);
            $values = $this->transform($values, $fields);
            foreach ($fields as $key => $value) {
                $props[$key] = $value['serializer']::toInternal($rawProps[$key]);
            }
            $instance = $this->manager->create($props);
            $this->manager->save($instance);
            $data = $this->retrieve($instance);
        }

        return ['data' => $data['data'], 'errors' => $errors];
    }


    public function update($instance)
    {
        $props = [];
        $fields = $this->getAllFields(); // yaml configurable
        $fields = $this->setFields($fields);
        $values = $this->getValuesByRequest();
        $errors = $this->validator($values, $fields, false); // early from yaml
        $data = null;
        if (0 === count($errors)) {
            $values = $this->setValues($values);
            $values = $this->transform($values, $fields);
            foreach ($fields as $key => $value) {
                $props[$key] = $value['serializer']::toInternal($values[$key]);
            }
            $this->manager->set($instance, $props);
            $this->manager->save($instance);
            $data = $this->manager->refresh($instance);
        }

        return ['data' => $data, 'errors' => $errors];
    }


    public function retrieve($instance)
    {
        $data = [];
        $errors = [];
        $fields = $this->getAllFields();
        foreach ($fields as $key => $value) {
            $data[$key] = $value['serializer']::toRepresentation($instance->getValue($key));
        }
        return ['data' => $data, 'errors' => $errors];
    }

    public function resolveAndCall($value, $args)
    {
        if (is_callable($value)) {
            return call_user_func($value, $args);
        }
        else if(is_string($value)) {
            $parts = explode("::", $value);

            // only method
            if (count($parts) === 1 && strpos($value, "::") === 0) {
                $class = $this;
                $method = $parts[0];
            }
            // class and method
            else if (count($parts) === 2) {
                $class = $parts[0];
                $method = $parts[1];
            }
            return call_user_func([$class, $method], $args);
        }
        
        throw \Exception('Not resolved');
    }


    public function getValuesByRequest() : array
    {
        return $this->request->getContent();
    }


    public function getUser() : array
    {
        return $this->context['user'];
    }
}
