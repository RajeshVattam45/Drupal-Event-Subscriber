<?php

namespace Drupal\blogs_email_notify\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\blogs_email_notify\Event\BlogCreatedEvent;
use Drupal\user\Entity\User;

class BlogsNodeCreateSubscriber implements EventSubscriberInterface {

  public static function getSubscribedEvents() {
    return [
      BlogCreatedEvent::EVENT_NAME => 'onBlogCreated',
    ];
  }

  public function onBlogCreated(BlogCreatedEvent $event) {
    $node = $event->getNode();
    $title = $node->label();
    $url = $node->toUrl('canonical', ['absolute' => TRUE])->toString();

    $user_ids = \Drupal::entityQuery('user')
      ->accessCheck(FALSE)
      ->condition('status', 1)
      ->condition('roles', 'content_editor')
      ->execute();

    $users = User::loadMultiple($user_ids);

    $mailManager = \Drupal::service('plugin.manager.mail');
    $module = 'blogs_email_notify';
    $key = 'blog_created_notification';

    foreach ($users as $user) {
      $to = $user->getEmail();
      $langcode = $user->getPreferredLangcode();
      $params = [
        'subject' => "New blog created: $title",
        'message' => "A new blog has been posted: \"$title\".\n\nView it here: $url",
      ];
      $mailManager->mail($module, $key, $to, $langcode, $params, NULL, TRUE);

      \Drupal::logger('blogs_email_notify')->info('Email sent to: @to', ['@to' => $to]);
    }

    \Drupal::logger('blogs_email_notify')->info('Processed BlogCreatedEvent for: @title', ['@title' => $title]);
  }
}
