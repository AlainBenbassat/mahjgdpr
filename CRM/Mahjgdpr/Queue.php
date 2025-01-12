<?php

class CRM_Mahjgdpr_Queue {
  public static function run() {
    $queue = self::getQueue();

    self::addTaskClearTargetGroup($queue);
    self::addTaskFindContactsWithPersonalEmail($queue);
    self::addTaskRemoveActiveContacts($queue);

    $queueRunner = self::getQueueRunner($queue);
    $queueRunner->runAllInteractive();
  }

  private static function getQueue() {
    return \Civi::queue('mahjgdpr-queue', [
      'type'  => 'Sql',
      'reset' => TRUE,
      'error' => 'abort',
    ]);
  }

  private static function getQueueRunner($queue) {
    return new CRM_Queue_Runner([
      'title' => 'MahJ RGPD : exécution ...',
      'queue' => $queue,
      'errorMode' => CRM_Queue_Runner::ERROR_ABORT,
      'onEnd' => ['CRM_Mahjgdpr_Queue', 'onEnd'],
      'onEndUrl' => CRM_Utils_System::url('civicrm', 'reset=1'),
    ], FALSE, NULL, FALSE);
  }

  private static function addTaskClearTargetGroup($queue) {
    $task = new CRM_Queue_Task(['CRM_Mahjgdpr_Group', 'clearTargetGroup']);
    $queue->createItem($task);
  }

  private static function addTaskFindContactsWithPersonalEmail($queue) {
    $task = new CRM_Queue_Task(['CRM_Mahjgdpr_Group', 'populateTargetGroup']);
    $queue->createItem($task);
  }

  private static function addTaskRemoveActiveContacts($queue) {
    $task = new CRM_Queue_Task(['CRM_Mahjgdpr_Group', 'removeActiveContactsFromTargetGroup']);
    $queue->createItem($task);
  }

  public static function onEnd(CRM_Queue_Queue $queue) {
    CRM_Core_Session::setStatus('queue is ok');
  }
}
