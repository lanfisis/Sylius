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

namespace Sylius\Bundle\CoreBundle\Form\Type\Customer;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class CustomerCheckoutGuestType extends AbstractResourceType
{
    /**
     * @param RepositoryInterface<CustomerInterface> $customerRepository
     * @param FactoryInterface<CustomerInterface> $customerFactory
     */
    public function __construct(
        string $dataClass,
        array $validationGroups,
        private RepositoryInterface $customerRepository,
        private FactoryInterface $customerFactory,
        private CanonicalizerInterface $canonicalizer,
    ) {
        parent::__construct($dataClass, $validationGroups);
    }

    public function buildForm(FormBuilderInterface $builder, array $options = []): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'sylius.form.customer.email',
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
                $data = $event->getData();

                if (!isset($data['email'])) {
                    return;
                }

                $emailCanonical = $this->canonicalizer->canonicalize($data['email']);

                /** @var CustomerInterface|null $customer */
                $customer = $this->customerRepository->findOneBy(['emailCanonical' => $emailCanonical]);

                // assign existing customer or create a new one
                if (null === $customer) {
                    /** @var CustomerInterface $customer */
                    $customer = $this->customerFactory->createNew();
                    $customer->setEmail($data['email']);
                }

                $form = $event->getForm();
                $form->setData($customer);
            })
            ->setDataLocked(false)
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_customer_checkout_guest';
    }
}
