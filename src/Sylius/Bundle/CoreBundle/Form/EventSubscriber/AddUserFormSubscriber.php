<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Form\EventSubscriber;

use Sylius\Component\User\Model\UserAwareInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Valid;
use Webmozart\Assert\Assert;

trigger_deprecation(
    'sylius/core-bundle',
    '1.14',
    'The "%s" class is deprecated and will be removed in Sylius 2.0.',
    AddUserFormSubscriber::class,
);

/** @deprecated since Sylius 1.14 and will be removed in Sylius 2.0. */
final class AddUserFormSubscriber implements EventSubscriberInterface
{
    public function __construct(private string $entryType)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::SUBMIT => 'submit',
        ];
    }

    public function preSetData(FormEvent $event): void
    {
        $form = $event->getForm();
        $form->add('user', $this->entryType, ['constraints' => [new Valid()]]);
        $form->add('createUser', CheckboxType::class, [
            'label' => 'sylius.ui.customer_can_login_to_the_store',
            'required' => false,
            'mapped' => false,
        ]);
    }

    public function submit(FormEvent $event): void
    {
        $data = $event->getData();
        $form = $event->getForm();

        /** @var UserAwareInterface $data */
        Assert::isInstanceOf($data, UserAwareInterface::class);

        if (null === $data->getUser()->getId() && null === $form->get('createUser')->getViewData()) {
            $data->setUser(null);
            $event->setData($data);

            $form->remove('user');
            $form->add('user', $this->entryType, ['constraints' => [new Valid()]]);
        }
    }
}
