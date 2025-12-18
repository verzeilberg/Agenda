<?php
namespace Agenda\Form;

use Agenda\Entity\Agenda;
use Blog\Entity\Blog;
use Blog\Entity\Category;
use Doctrine\Laminas\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\Persistence\ObjectManager;
use DoctrineModule\Form\Element\ObjectMultiCheckbox;
use DoctrineModule\Form\Element\ObjectSelect;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Collection;
use Laminas\Form\Element\Color;
use Laminas\Form\Element\Date;
use Laminas\Form\Element\File;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Textarea;
use Laminas\Form\Element\Time;
use Laminas\Form\Fieldset;
use Laminas\InputFilter\InputFilterProviderInterface;

class AgendaFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('agenda-import');

        $this->setHydrator(new DoctrineHydrator($objectManager))
            ->setObject(new Agenda());

        $this->add([
            'type'  => Text::class,
            'name' => 'title',
            'options' => [
                'label' => 'Title',
            ],
            'attributes' => [
                'class' => 'form-control',
            ],
        ]);

        $this->add([
            'type' => Color::class,
            'name' => 'color',
            'options' => [
                'label' => 'Color',
            ],
            'attributes' => [
                'class' => 'form-control',
                'id' => 'colors'
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
            'title' => [
                'required' => true,
            ],
        ];
    }
}
