<?php

namespace Api;

use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class LoginForm extends Form implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct();

        $this
            ->add([
                'name' => 'platform',
                'type' => 'text',
            ])
            ->add([
                'name' => 'identity',
                'type' => 'text',
            ])
            ->add([
                'name' => 'credential',
                'type' => 'password',
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getInputFilterSpecification()
    {
        return [
            'identity' => [
                'required' => true,
            ],
            'credential' => [
                'required' => true,
            ],
            'platform' => [
                'required' => true,
            ]
        ];
    }
}
