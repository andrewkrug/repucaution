<?php

namespace spec\PlanFeaturesAcl\Validator\Constraint;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PlanFeaturesAcl\Feature\FeatureInterface;

class BoolSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PlanFeaturesAcl\Validator\Constraint\Bool');
        $this->shouldHaveType('PlanFeaturesAcl\Validator\FeatureValidatorInterface');
    }

    function it_should_validate_every_value(FeatureInterface $feature)
    {
        $this->validate($feature, null, null)->shouldReturn(true);
        $this->validate($feature, null, 100)->shouldReturn(true);
        $this->validate($feature, 100, false)->shouldReturn(true);
    }

}
