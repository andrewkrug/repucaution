<?php

namespace spec\PlanFeaturesAcl\Validator\Constraint;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PlanFeaturesAcl\Feature\FeatureInterface;

class NumericSpec extends ObjectBehavior
{
    function let(
        FeatureInterface $ltNumericFeature,
        FeatureInterface $lteNumericFeature,
        FeatureInterface $gtNumericFeature,
        FeatureInterface $gteNumericFeature,
        FeatureInterface $eqNumericFeature,
        FeatureInterface $neqNumericFeature,
        FeatureInterface $waNumericFeature
    ) {

        $ltNumericFeature->getType()->willReturn('numeric');
        $ltNumericFeature->getValidationRules()->willReturn(json_encode(
            array(
                'lt'
            )
        ));

        $lteNumericFeature->getType()->willReturn('numeric');
        $lteNumericFeature->getValidationRules()->willReturn(json_encode(
            array(
                'lte'
            )
        ));

        $gtNumericFeature->getType()->willReturn('numeric');
        $gtNumericFeature->getValidationRules()->willReturn(json_encode(
            array(
                'gt'
            )
        ));

        $gteNumericFeature->getType()->willReturn('numeric');
        $gteNumericFeature->getValidationRules()->willReturn(json_encode(
            array(
                'gte'
            )
        ));

        $eqNumericFeature->getType()->willReturn('numeric');
        $eqNumericFeature->getValidationRules()->willReturn(json_encode(
            array(
                'eq'
            )
        ));

        $neqNumericFeature->getType()->willReturn('numeric');
        $neqNumericFeature->getValidationRules()->willReturn(json_encode(
            array(
                'neq'
            )
        ));

        $waNumericFeature->getType()->willReturn('numeric');
        $waNumericFeature->getValidationRules()->willReturn(null);

    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PlanFeaturesAcl\Validator\Constraint\Numeric');
        $this->shouldHaveType('PlanFeaturesAcl\Validator\FeatureValidatorInterface');
    }

    function it_should_throw_invalid_argument_exception_if_it_is_not_numeric(FeatureInterface $ltNumericFeature)
    {
        $this->shouldThrow('\InvalidArgumentException')
            ->duringValidate($ltNumericFeature, 1, 'a string');

        $this->shouldThrow('\InvalidArgumentException')
            ->duringValidate($ltNumericFeature, 'a string', 1);
    }

    function it_should_validate_if_validation_rules_empty(FeatureInterface $waNumericFeature)
    {
        $this->validate($waNumericFeature, 0, 100)->shouldReturn(true);
    }


    function it_should_process_less_than_rule(FeatureInterface $ltNumericFeature)
    {
        $this->validate($ltNumericFeature, '10', '9')->shouldReturn(true);
        $this->validate($ltNumericFeature, '11.5', '11.4')->shouldReturn(true);

        $this->validate($ltNumericFeature, '10', '11')->shouldReturn(false);
        $this->validate($ltNumericFeature, '11.5', '11.5')->shouldReturn(false);
    }

    function it_should_process_less_than_equal_rule(FeatureInterface $lteNumericFeature)
    {
        $this->validate($lteNumericFeature, '10', 10)->shouldReturn(true);
        $this->validate($lteNumericFeature, '11.5', 11.5)->shouldReturn(true);
        $this->validate($lteNumericFeature, '11.5', 7.5)->shouldReturn(true);

        $this->validate($lteNumericFeature, '10', '11')->shouldReturn(false);
        $this->validate($lteNumericFeature, '11.5', 11.6)->shouldReturn(false);
    }

    function it_should_process_greater_than_rule(FeatureInterface $gtNumericFeature)
    {
        $this->validate($gtNumericFeature, '10', '11')->shouldReturn(true);
        $this->validate($gtNumericFeature, '11.5', '11.6')->shouldReturn(true);

        $this->validate($gtNumericFeature, '10', '10')->shouldReturn(false);
        $this->validate($gtNumericFeature, '11.5', '11.5')->shouldReturn(false);
    }

    function it_should_process_greater_than_equal_rule(FeatureInterface $gteNumericFeature)
    {
        $this->validate($gteNumericFeature, '10', 10)->shouldReturn(true);
        $this->validate($gteNumericFeature, '11.5', 11.5)->shouldReturn(true);
        $this->validate($gteNumericFeature, '11.5', 17.5)->shouldReturn(true);

        $this->validate($gteNumericFeature, '10', '9')->shouldReturn(false);
        $this->validate($gteNumericFeature, '11.5', 11.4)->shouldReturn(false);
    }

    function it_should_process_equal_rule(FeatureInterface $eqNumericFeature)
    {
        $this->validate($eqNumericFeature, '10', 10)->shouldReturn(true);
        $this->validate($eqNumericFeature, '11.5', 11.5)->shouldReturn(true);

        $this->validate($eqNumericFeature, '10', '11')->shouldReturn(false);
        $this->validate($eqNumericFeature, '11.5', 0)->shouldReturn(false);
    }

    function it_should_process_not_equal_rule(FeatureInterface $neqNumericFeature)
    {
        $this->validate($neqNumericFeature, '10', 10)->shouldReturn(false);
        $this->validate($neqNumericFeature, '11.5', 11.5)->shouldReturn(false);

        $this->validate($neqNumericFeature, '10', '11')->shouldReturn(true);
        $this->validate($neqNumericFeature, '11.5', 0)->shouldReturn(true);
    }

}
