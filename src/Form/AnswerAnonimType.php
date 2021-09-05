<?php
/**
 * Answer type.
 */

namespace App\Form;

use App\Entity\Answer;
use App\Entity\Task;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use App\Service\AnswerService;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * Class AnswerType.
 */
class AnswerAnonimType extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder The form builder
     * @param array                                        $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {


        $builder->add(
            'task',
            EntityType::class,
            [
                'class' => Task::class,
                'choice_label' => function ($task) {
                    return $task->getTitle();
                },
                'label' => 'label_task',
                'placeholder' => 'label_none',
                'required' => true,
            ]
        );

        $builder->add(
            'nick',
            TextType::class,
            [
                'label' => 'label_author',
                'attr' => ['max_length' => 64],
                'required' => false,
            ]
        );

        $builder->add(
            'text',
            TextType::class,
            [
                'label' => 'label_content',
                'required' => true,
                'attr' => ['max_length' => 255],
            ]
        );
    }

//        // if (is_granted('ROLE_ADMIN')){
//        $builder->add(
//            'indication',
//            HiddenType::class,
//            [
//                'data' => 0,
//            ]
//        );
//    }
////    }

    /**
     * Configures the options for this type.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Answer::class]);
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix(): string
    {
        return 'answer';
    }
}