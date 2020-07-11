<?php
namespace Brana\CmfBundle\Store\Serializer;

use Brana\CmfBundle\Store\Entity\IBranaEntity;
use Brana\CmfBundle\Store\Manager\IManager;

class ContentSerializer implements IBranaSerializer
{
    private $fieldMapping;
    private $params;

    public function __construct(IManager $manager, array $context) {
        $this->manager = $manager;
        $this->context = $context;
        $this->params = $context['params'];
        $this->request = $context['request'];
        $this->fieldMapping = [
            'integer' => Field\IntegerSerializer::class,
            'text' => Field\TextSerializer::class,
            'date' => Field\DateSerializer::class,
            'slug' => Field\TextSerializer::class,
            'boolean' => Field\TextSerializer::class,
            'choice' => Field\ChoiceSerializer::class,
            'relation' => Field\RelationSerializer::class,
        ];
    }


    public function getAllFields()
    {
        $fields = [];
        $ctName = $this->manager->getContentTypeName();
        foreach ($this->manager->getFields() as $key => $fieldModel) {
            $fieldSerializer = $this->fieldMapping[$fieldModel->getModel()['type']];
            $fields[$key] = [
                'serializer' => $fieldSerializer,
                'model' =>  $fieldModel,
                'required' => false,
                'read_only' => false,
                'write_only' => false,
                'match' => false,
            ];
            if (isset($this->params[$ctName]['fields'][$key])) {
                $fields[$key] = array_merge($fields[$key], $this->params[$ctName]['fields'][$key]);
            }
        }
        return $fields;
    }


    public function getFields($keys)
    {
        $fields = [];
        $ctName = $this->manager->getContentTypeName();
        foreach ($keys as $key) {
            $fieldSerializer = null;
            $fieldModel = $this->manager->getField($key);
            if (isset($fieldModel)) {
                $type = $this->manager->getField($key)->getModel()['type'];
                $fieldSerializer = $this->fieldMapping[$type];
            }
            $fields[$key] = [
                'serializer' => $fieldSerializer,
                'model' =>  $fieldModel,
                'required' => false,
                'read_only' => false,
                'write_only' => false,
                'match' => false,
            ];
            if (isset($this->params[$ctName]['fields'][$key])) {
                $fields[$key] = array_merge($fields[$key], $this->params[$ctName]['fields'][$key]);
            }
        }
        return $fields;
    }

    public function getFieldsKeys() {
        $ctName = $this->manager->getContentTypeName();
        $fKeys = $this->params[$ctName]['fields_keys'] ?? null;
        if (is_array($fKeys)) {
            return $fKeys;
        }
        else if ($fKeys === null) {
            return array_keys($this->params[$ctName]['fields']);
        }
        else if ($fKeys === 'all') {
            return array_keys($this->manager->getFields());
        }
        else if (is_string($fKeys) && strpos($fKeys, '::') !== false) {
            return $this->resolveAndCall($fKeys);
        };
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
            if (isset($value['transform'])) {
                $data[$key] = $this->resolveAndCall($value['transform'], ...$data[$key]);
            }
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
            if (isset($value['constraints'])) {
                $constraintsErrors = $this->validateField($value['constraints'], $key, $data[$key]);
                $errors[$key] = array_merge($errors[$key], $constraintsErrors);
                if ($early && 0 !== count($errors[$key])) {
                    return $errors;
                }
            }
            if (0 === count($errors[$key])) {
                unset($errors[$key]);
            }
        }
        return $errors;
    }


    public function create()
    {
        $props = [];
        $fieldKeys = $this->getFieldsKeys();
        $fields = $this->getFields($fieldKeys);
        $fields = $this->setFields($fields);
        $values = $this->getValuesByRequest();
        $errors = $this->validator($values, $fields, false); // early from yaml
        $data = null;
        if (0 === count($errors)) {
            $values = $this->setValues($values);
            $values = $this->transform($values, $fields);
            $instance = $this->manager->create($props);
            $this->unserialize($fields, $values, $instance);
            $this->manager->save($instance);
            $data = $this->serialize($instance, $fields);
        }

        return ['data' => $data['data'], 'errors' => $errors];
    }


    public function update(IBranaEntity $instance)
    {
        $fieldKeys = $this->getFieldsKeys();
        $fields = $this->getFields($fieldKeys);
        $fields = $this->setFields($fields);
        $values = $this->getValuesByRequest();
        $errors = $this->validator($values, $fields, false); // early from yaml
        $data = null;
        if (0 === count($errors)) {
            $values = $this->setValues($values);
            $values = $this->transform($values, $fields);
            $this->unserialize($fields, $values, $instance);
            $this->manager->save($instance);
            // todo: handle errors and data
            $data = $this->serialize($instance, $fields);
        }

        return ['data' => $data['data'], 'errors' => $errors];
    }


    public function retrieve(IBranaEntity $instance)
    {
        $fieldKeys = $this->getFieldsKeys();
        $fields = $this->getFields($fieldKeys);
        $fields = $this->setFields($fields);
        return $this->serialize($instance,  $fields);
    }


    public function serialize(IBranaEntity $instance, $fields)
    {
        $data = [];
        $errors = [];
        foreach ($fields as $key => $value) {
            if ($value['write_only']) {
                continue;
            }
            $data[$key] = $value['serializer']::toRepresentation($instance->get($key), $value);
        }
        return ['data' => $data, 'errors' => $errors];
    }

    public function unserialize($fields, $values, IBranaEntity $instance)
    {
        foreach ($fields as $key => $value) {
            if ($value['read_only']) {
                continue;
            }
            if (isset($values[$key])) {
                $res = $value['serializer']::toInternal($values[$key], $value);
                $instance->set($key, $res);
            }
        }
        return $instance;
    }


    public function resolveAndCall($value, ...$args)
    {
        if (is_callable($value)) {
            return call_user_func($value, $args);
        }
        else if(is_string($value)) {
            $parts = explode("::", $value);

            // only method
            if (strpos($value, "::") === 0) {
                $class = $this;
                $method = $parts[1];
            }
            // class and method
            else if (isset($parts[0]) && isset($parts[1])) {
                $class = $parts[0];
                $method = $parts[1];
            }
            return call_user_func([$class, $method], ...$args);
        }
        
        throw \Exception('Not resolved');
    }

    // handle error from bad json
    public function getValuesByRequest() : array
    {   
        $json = json_decode($this->request->getContent(), true);
        if ($json === null) {
            $json = [];
        }
        return $json;
    }


    public function getUser() : array
    {
        return $this->context['user'];
    }
}
