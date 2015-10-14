<div class="main sign_in">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
                <div class="main_block m-t30">
                    <h1 class="head_title">Confirmation</h1>
                    <form id="payment-period-plan" method="POST" action="<?php echo site_url('subscript/transaction/'.$user->id.'/'.$plan->id); ?>">
                        <table class="table crm_profile m-t20 custom-form">
                            <tbody>
                            <tr>
                                <td>
                                    <p class="gray-color bold">Full Name</p>
                                </td>
                                <td>
                                    <p><?php echo $user->first_name;?> <?php echo $user->last_name;?></p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p class="gray-color bold">Email:</p>
                                </td>
                                <td>
                                    <p><?php echo $user->email;?></p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p class="gray-color bold">Subscription Plan:</p>
                                </td>
                                <td>
                                    <p><?php echo $plan->name;?></p>
                                    <?php $i=1;?>
                                    <?php foreach ($periods as $period) :?>
                                        <label class="cb-radio w-100">
                                            <input type="radio" name=plan_period value="<?php echo $period->id?>" <?php echo ($i == 1) ? 'checked' : '';?>>
                                            <?php echo $period->period.' '.$options[$period->qualifier].'(s) / $'.$period->viewPrice()?>
                                        </label>
                                        <?php $i++;?>
                                    <?php endforeach;?>
                                    <div class="row" style="display: none;">
                                        <div class="col-sm-6">
                                            <select name="system" class="chosen-select m-t10">
                                                <?php foreach($systems as $key=>$system): ?>
                                                    <option value="<?= $key ?>"><?php echo $system ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-xs-12 text-center">
                                <button class="btn btn-save">Pay</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>