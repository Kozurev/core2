<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 18.10.2018
 * Time: 16:53
 */

global $CFG;

$groups = Schedule_Group::query()
    ->where('type', '=', Schedule_Group::TYPE_LIDS)
    ->where('date', '>=', date('Y-m-d'))
    ->get();

?>

<div class="lids">
    <?php
    Core_Page_Show::instance()->execute();
    ?>
</div>

<div class="modal fade" id="lidsGroupsModal" tabindex="-1" role="dialog" aria-labelledby="lidsGroupsLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lidsGroupsLabel">Группы лидов</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&#215;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="lids_groups_ajax_form" method="POST" action="<?=$CFG->wwwroot?>/api/groups/index.php?action=appendToGroup">
                    <input type="hidden" name="object_id" value="" />
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <?php
                                if ($groups->isNotEmpty()) {
                                ?>
                                    <select class="form-control selectpicker" data-live-search="true" name="group_id">
                                        <?php
                                        /** @var Schedule_Group $group */
                                        foreach ($groups as $group) {
                                            echo '<option value="'.$group->getId().'">'.$group->title() . ' (' . refactorDateFormat($group->date()) . ' / ' . refactorTimeFormat($group->timeStart()) .')</option>';
                                        }
                                        ?>
                                    </select>
                                <?php
                                } else {
                                ?>
                                    <p>Групп лидов не найдено</p>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Отменить</button>
                <button type="button" class="btn btn-primary" onclick="$('#lids_groups_ajax_form').submit(); return false;">Добавить в группу</button>
            </div>
        </div>
    </div>
</div>