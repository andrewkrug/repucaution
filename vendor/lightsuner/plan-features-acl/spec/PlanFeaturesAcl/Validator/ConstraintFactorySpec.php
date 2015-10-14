<?php

namespace spec\PlanFeaturesAcl\Validator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ConstraintFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PlanFeaturesAcl\Validator\ConstraintFactoryInterface');
    }

    function it_should_return_an_instance_of_bool_validator()
    {
        $this->getConstraint('bool')->shouldBeAnInstanceOf('PlanFeaturesAcl\Validator\Constraint\Bool');
    }
    function it_should_return_an_instance_of_numeric_validator()
    {
        $this->getConstraint('numeric')->shouldBeAnInstanceOf('PlanFeaturesAcl\Validator\Constraint\Numeric');
    }

    function it_should_return_an_instance_of_numeric_validator_case_insensitive()
    {
        $this->getConstraint('nUmErIc')->shouldBeAnInstanceOf('PlanFeaturesAcl\Validator\Constraint\Numeric');
    }

    function it_should_throw_a_runtime_exception_on_unknown_type()
    {
        $this->shouldThrow('\RuntimeException')
            ->duringGetConstraint('unknown_type');
    }
}
