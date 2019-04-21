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
            'boolean' => Field\TextSerializer::class,
            'choice' => Field\ChoiceSerializer::class,
        ];
    }


    public function getAllFields()
    {
        $fields = [];
        $ctName = $this->manager->getContentTypeName();
        foreach ($this->manager->getContentType()['fields'] as $key => $value) {
            $fieldSerializer = $this->fieldMapping[$value['type']];
            $fields[$key] = [
                'serializer' => $fieldSerializer,
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
        $ctName = $this->getContentTypeName();
        foreach ($keys as $ct) {
            $type = $this->manager->getContentType()['fields'][$ct]['type'];
            $fieldSerializer = $this->fieldMapping[$type];
            $fields[$key] = [
                'serializer' => $fieldSerializer,
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
                $data[$key] = $this->resolveAndCall($value['transform'], $data[$key]);
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
        $fields = $this->getAllFields();
        $fields = $this->setFields($fields);
        $values = $this->getValuesByRequest();
        $errors = $this->validator($values, $fields, false); // early from yaml
        $data = null;
        if (0 === count($errors)) {
            $values = $this->setValues($values);
            $values = $this->transform($values, $fields);
            foreach ($fields as $key => $value) {
                if (isset($values[$key])) {
                    $props[$key] = $value['serializer']::toInternal($values[$key]);
                }
            }
            $instance = $this->manager->create($props);
            $this->manager->save($instance);
            $data = $this->retrieve($instance);
        }

        return ['data' => $data['data'], 'errors' => $errors];
    }


    public function update($instance)
    {
        $fields = $this->getAllFields(); // yaml configurable
        $fields = $this->setFields($fields);
        $values = $this->getValuesByRequest();
        $errors = $this->validator($values, $fields, false); // early from yaml
        $data = null;
        if (0 === count($errors)) {
            $values = $this->setValues($values);
            $values = $this->transform($values, $fields);
            foreach ($fields as $key => $value) {
                if ($value['read_only']) {
                    continue;
                }
                if (isset($values[$key])) {
                    $res = $value['serializer']::toInternal($values[$key], $value);
                    $instance->set($key, $res);
                }
            }
            $this->manager->save($instance);
            $data = $this->retrieve($instance);
        }

        return ['data' => $data, 'errors' => $errors];
    }


    public function retrieve($instance)
    {
        $data = [];
        $errors = [];
        $fields = $this->getAllFields();
        foreach ($fields as $key => $value) {
            $data[$key] = $value['serializer']::toRepresentation($instance->get($key), $value);
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

    // handle error from bad json
    public function getValuesByRequest() : array
    {   
        return json_decode($this->request->getContent(), true);
    }


    public function getUser() : array
    {
        return $this->context['user'];
    }
}
