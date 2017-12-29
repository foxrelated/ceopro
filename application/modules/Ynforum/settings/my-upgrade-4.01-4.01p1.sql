UPDATE `engine4_core_modules` SET `version` = '4.01p1' WHERE `name` = 'ynforum';

INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
('ynforum_topic_reputation', 'ynforum', '{item:$subject} added reputation for your {item:$object:post} on the {itemParent:$object::forum topic}.', 0, '');

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('ynforum_post_thank', 'ynforum', '{item:$subject} thanked to a post in the {item:$object:topic} in the {itemParent:$object:forum}: {body:$body}', 1, 5, 1, 1, 1, 1);
