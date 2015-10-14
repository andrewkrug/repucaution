<?php

namespace spec\PlanFeaturesAcl\Validator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PlanFeaturesAcl\Feature\AttachedInterface;
use PlanFeaturesAcl\Feature\FeatureInterface;
use PlanFeaturesAcl\Validator\ConstraintFactoryInterface;
use PlanFeaturesAcl\Validator\FeatureValidatorInterface;

class FeatureValidatorSpec extends ObjectBehavior
{
    function let(
        ConstraintFactoryInterface $constraintFactory,
        FeatureValidatorInterface $boolConstraint,
        FeatureInterface $boolFeature
    ) {

        $this->beConstructedWith($constraintFactory);

        $constraintFactory->getConstraint('bool')->willReturn($boolConstraint);

        $boolConstraint->validate(Argument::any(), Argument::any(), Argument::any())->willReturn(true);

        $boolFeature->getType()->willReturn('bool');
        $boolFeature->getValidationRules()->willReturn(null);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PlanFeaturesAcl\Validator\FeatureValidatorInterface');
    }

    function it_should_use_factory_object(
        FeatureInterface $boolFeature,
        ConstraintFactoryInterface $constraintFactory
    ) {

        $bf = $boolFeature->getWrappedObject();

        $constraintFactory
            ->getConstraint($bf->getType())
            ->shouldBeCalled();

        $this->validate($boolFeature, null, null);
    }

}
