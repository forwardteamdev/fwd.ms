<?php
/**
 * @author Serghei Luchianenco (s@luchianenco.com)
 * Date: 29/05/2017
 * Time: 12:19
 */

namespace AppBundle\Form\Type;

use AppBundle\Document\UserInvitation;
use AppBundle\Form\DataTransformer\InvitationToCodeTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserInvitationFormType extends AbstractType
{
    private $invitationTransformer;

    public function __construct(InvitationToCodeTransformer $invitationTransformer)
    {
        $this->invitationTransformer = $invitationTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->invitationTransformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'class' => UserInvitation::class,
            'required' => true,
        ));
    }

    public function getParent()
    {
        return TextType::class;
    }

    public function getBlockPrefix()
    {
        return 'app_invitation_type';
    }
}
