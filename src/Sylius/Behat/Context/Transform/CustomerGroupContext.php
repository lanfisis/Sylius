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

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Webmozart\Assert\Assert;

final class CustomerGroupContext implements Context
{
    public function __construct(private RepositoryInterface $customerGroupRepository)
    {
    }

    /**
     * @Transform :customerGroup
     * @Transform /^group "([^"]+)"$/
     * @Transform /^"([^"]+)" group$/
     */
    public function getCustomerGroupByName($customerGroupName)
    {
        $customerGroup = $this->customerGroupRepository->findOneBy(['name' => $customerGroupName]);

        Assert::notNull($customerGroup, sprintf('Cannot find customer group with name %s', $customerGroupName));

        return $customerGroup;
    }
}
