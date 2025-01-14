<?php

class CRM_Mahjgdpr_Queue {
  public static function run() {
    $queue = self::getQueue();

    self::addTaskClearTargetGroup($queue);
    self::addTaskFindContactsWithPersonalEmail($queue);
    self::addTaskRemoveActiveContacts($queue);

    $queueRunner = self::getQueueRunner($queue);
    $queueRunner->runAllViaWeb();
  }

  private static function getQueue() {
    return CRM_Queue_Service::singleton()->create([
      'name' => 'mahjgdpr-queue',
      'type'  => 'Sql',
      'reset' => TRUE,
      'error' => 'abort',
      'runner' => 'task',
    ]);
  }

  private static function getQueueRunner($queue) {
    return new CRM_Queue_Runner([
      'title' => 'MahJ RGPD : exécution ...',
      'queue' => $queue,
      'errorMode' => CRM_Queue_Runner::ERROR_ABORT,
      'onEnd' => ['CRM_Mahjgdpr_Queue', 'onEnd'],
      'onEndUrl' => CRM_Utils_System::url('civicrm', 'reset=1'),
    ]);
  }

  private static function addTaskClearTargetGroup($queue) {
    $task = new CRM_Queue_Task(['CRM_Mahjgdpr_Queue', 'clearTargetGroup']);
    $queue->createItem($task);
  }

  private static function addTaskFindContactsWithPersonalEmail($queue) {
    $task = new CRM_Queue_Task(['CRM_Mahjgdpr_Queue', 'populateTargetGroup']);
    $queue->createItem($task);
  }

  private static function addTaskRemoveActiveContacts($queue) {
    $task = new CRM_Queue_Task(['CRM_Mahjgdpr_Queue', 'removeActiveContactsFromTargetGroup']);
    $queue->createItem($task);
  }

  public static function onEnd(CRM_Queue_TaskContext $ctx) {
    CRM_Core_Session::setStatus('queue is ok');
  }

  public static function clearTargetGroup() {
    $group = new CRM_Mahjgdpr_Group();
    $group->clear();

    return TRUE;
  }

  public static function populateTargetGroup() {
    $group = new CRM_Mahjgdpr_Group();
    $group->populate();

    return TRUE;
  }

  public static function removeActiveContactsFromTargetGroup() {
    return TRUE;
  }
}
