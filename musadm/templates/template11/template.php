<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 18.10.2018
 * Time: 16:53
 */

if( User::checkUserAccess( ["superuser" => "1"] ) )
{
    $this->execute();
}

if( User::checkUserAccess( ["groups" => [2, 6]] ) )
{
    ?>


    <div class="row">
        <div class="col-lg-6">
            <section class="cards-section text-center">
                <div id="cards-wrapper" class="cards-wrapper row">
                    <?

                    ?>
                </div>
            </section>
        </div>
    </div>


    <?
}