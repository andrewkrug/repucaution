<?php

namespace spec\PlanFeaturesAcl;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use PlanFeaturesAcl\Plan\PlanInterface;
use \PlanFeaturesAcl\Validator\FeatureValidatorInterface;
use \PlanFeaturesAcl\Feature\AttachedInterface;
use \PlanFeaturesAcl\Feature\FeatureInterface;

class ProviderSpec extends ObjectBehavior
{

    function let(
        FeatureValidatorInterface $validator,
        PlanInterface $plan,
        AttachedInterface $attachedFeature,
        FeatureInterface $feature
    ) {

        $this->beConstructedWith($validator);

        $feature->getSlug()->willReturn('first_feature');

        $attachedFeature->getValue()->willReturn(null);
        $attachedFeature->getFeature()->willReturn($feature);

        $af = $attachedFeature->getWrappedObject();

        $validator->validate($af->getFeature(), $af->getValue(), null)
            ->willReturn(true);

        $plan->getAttachedFeatures()->willReturn(array($attachedFeature));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PlanFeaturesAcl\ProviderInterface');
    }

    function it_should_allow_set_plan(PlanInterface $plan)
    {

        $plan->getAttachedFeatures()->shouldBeCalled();

        $this->setPlan($plan);
    }

    function it_should_be_enabled()
    {
        $this->enable();
        $this->shouldBeEnable();
    }

    function it_should_be_disable_by_default()
    {
        $this->isEnable()->shouldReturn(false);
    }

    function it_should_always_allow_access_if_disabled()
    {
        $this->isGranted('something')->shouldReturn(true);
        $this->hasFeature('something')->shouldReturn(true);
    }


    function it_should_validate_access(
        PlanInterface $plan,
        AttachedInterface $attachedFeature,
        FeatureValidatorInterface $validator
    ) {

        $this->enable();
        $this->setPlan($plan);

        $af = $attachedFeature->getWrappedObject();

        $validator
            ->validate($af->getFeature(), $af->getValue(), null)
            ->shouldBeCalled();

        $this->isGranted('first_feature')->shouldReturn(true);
    }

    function it_should_not_valid_anything_with_empty_plan()
    {
        $this->enable();
        $this->setPlan(null);

        $this->isGranted('first_feature')->shouldReturn(false);
    }

    function it_should_check_for_feature_in_plan(PlanInterface $plan)
    {
        $this->enable();
        $this->setPlan($plan);
        $this->hasFeature('not_existing_feature')->shouldReturn(false);
        $this->hasFeature('first_feature')->shouldReturn(true);
    }

    function it_should_throw_exception_when_getting_value_of_nonexistent_feature(PlanInterface $plan)
    {
        $this->enable();
        $this->setPlan($plan);
        $this->shouldThrow('\RuntimeException')
        ->duringGetFeatureValue('unknown_type');
    }

    function it_should_return_real_attachemt_value(PlanInterface $plan, AttachedInterface $attachedFeature)
    {
        $af = $attachedFeature->getWrappedObject();

        $this->enable();
        $this->setPlan($plan);
        $this->getFeatureValue('first_feature')->shouldReturn($af->getValue());
    }

    function it_should_always_return_null_if_provider_is_disabled()
    {
        $this->getFeatureValue('unknown_type')->shouldReturn(null);
    }

}
