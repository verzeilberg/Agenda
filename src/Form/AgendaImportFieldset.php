<?php
namespace Agenda\Form;

use Blog\Entity\Blog;
use Blog\Entity\Category;
use Doctrine\Laminas\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\Persistence\ObjectManager;
use DoctrineModule\Form\Element\ObjectMultiCheckbox;
use DoctrineModule\Form\Element\ObjectSelect;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Collection;
use Laminas\Form\Element\Date;
use Laminas\Form\Element\File;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Laminas\Form\Element\Time;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;

class AgendaImportFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('agenda-import');

        $this->add([
            'type'  => Text::class,
            'name' => 'url',
            'options' => [
                'label' => 'URL',
            ],
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);
    }

    /**
     * Provides the input filter specification for validating and filtering input data.
     *
     * @return array The specification array containing validation rules.
     */
    public function getInputFilterSpecification(): array
    {
        return [
            'url' => [
                'required' => true,
            ],
        ];
    }
}
