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

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Webmozart\Assert\Assert;

final class CustomerRegistrationFormSubscriber implements EventSubscriberInterface
{
    /** @param RepositoryInterface<CustomerInterface> $customerRepository */
    public function __construct(private RepositoryInterface $customerRepository)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function preSubmit(FormEvent $event): void
    {
        $rawData = $event->getData();
        $form = $event->getForm();
        $data = $form->getData();

        Assert::isInstanceOf($data, CustomerInterface::class);

        // if email is not filled in, go on
        if (!isset($rawData['email']) || empty($rawData['email'])) {
            return;
        }

        /** @var CustomerInterface|null $existingCustomer */
        $existingCustomer = $this->customerRepository->findOneBy(['email' => $rawData['email']]);
        if (null === $existingCustomer || null !== $existingCustomer->getUser()) {
            return;
        }

        $existingCustomer->setUser($data->getUser());
        $form->setData($existingCustomer);
    }
}
