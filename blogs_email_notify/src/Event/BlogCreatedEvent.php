<?php

namespace Drupal\blogs_email_notify\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Drupal\node\NodeInterface;

class BlogCreatedEvent extends Event {

  public const EVENT_NAME = 'blogs_email_notify.blog_created';

  protected $node;

  public function __construct(NodeInterface $node) {
    $this->node = $node;
  }

  public function getNode(): NodeInterface {
    return $this->node;
  }
}
