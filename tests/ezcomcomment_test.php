<?php
//
// Created on: <09-Dec-2009 00:00:00 xc>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ Comments extension for eZ Publish
// SOFTWARE RELEASE: 1.0-1
// COPYRIGHT NOTICE: Copyright (C) 2009 eZ Systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
// 
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
// 
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
// 
// 
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//

class ezcomCommentTest extends ezpDatabaseTestCase
{
    /**
     * Path to the DB schema.
     * 
     * @var array
     */
    protected $sqlFiles = array( array( 'extension/ezcomments/sql/', 'schema.sql' ) );

    public function __construct()
    {
        parent::__construct();
        $this->setName( "ezcomComment object test" );
    }

    public function setUp()
    {
        parent::setUp();

        ezpTestDatabaseHelper::insertSqlData( $this->sharedFixture, $this->sqlFiles );
    }

    /**
     * 1. Create a new comment object with dummy data
     * 2. Check if data are stored properly
     * 
     */
    public function testCreateObject()
    {
        // Create a new comment
        $comment = ezcomComment::create();
        $comment->setAttribute( 'contentobject_id', 12 );
        $comment->setAttribute( 'language_id', 2 );
        $comment->setAttribute( 'created', 21213423 );
        $comment->setAttribute( 'modified', 21321231 );
        $comment->setAttribute( 'user_id', 14 );
        $comment->setAttribute( 'session_key', 'a2e4822a98337283e39f7b60acf85ec9' );
        $comment->setAttribute( 'ip', '10.0.2.122' );
        $comment->setAttribute( 'name', 'xc' );
        $comment->setAttribute( 'email', 'xc@ez.no' );
        $comment->setAttribute( 'url', 'http://ez.no' );
        $comment->setAttribute( 'text', 'ezcomComment object test comment.' );
        $comment->setAttribute( 'notification', 1 );
        $comment->store();

        $this->assertType( 'ezcomComment', $comment );
        $this->assertEquals( 12, $comment->attribute( 'contentobject_id' ) );
        $this->assertEquals( 2, $comment->attribute( 'language_id' ) );
        $this->assertEquals( 21213423, $comment->attribute( 'created' ) );
        $this->assertEquals( 21321231, $comment->attribute( 'modified' ) );
        $this->assertEquals( 14, $comment->attribute( 'user_id' ) );
        $this->assertEquals( 'a2e4822a98337283e39f7b60acf85ec9', $comment->attribute( 'session_key' ) );
        $this->assertEquals( '10.0.2.122', $comment->attribute( 'ip' ) );
        $this->assertEquals( 'xc', $comment->attribute( 'name' ) );
        $this->assertEquals( 'xc@ez.no', $comment->attribute( 'email' ) );
        $this->assertEquals( 'http://ez.no', $comment->attribute( 'url' ) );
        $this->assertEquals( 'ezcomComment object test comment.', $comment->attribute( 'text' ) );
        $this->assertEquals( 1, $comment->attribute( 'notification' ) );
    }

    /**
     * 1. Fetch ezcomComment object
     * 2. Check is object is instance of ezcomComment class
     * 3. Fetch ezcomComment object which does not exist
     * 4. Check if result equals to null
     *
     */
    public function testFetchObject()
    {
        $comment = ezcomComment::fetch( 1 );
        $this->assertType( 'ezcomComment', $comment );
        
        $comment = ezcomComment::fetch( 2 );
        $this->assertEquals( null, $comment );
    }
    
    /**
     * 1. store an ezcomcomment object into database
     * 2. Fetch the ezcomComment object list
     * 3. assert fetchForUser( userid )
     * 4. Assert fetchForUser( userid, notification )
     * 5. Assert fetchForUser( userid,notification, status )
     * 6. Assert fetchForUser( userid ) when there is no record in database
     */
    public function testFetchForUser()
    {
        // Create a new comment
        $comment = ezcomComment::create();
        $comment->setAttribute( 'contentobject_id', 13 );
        $comment->setAttribute( 'language_id', 2 );
        $comment->setAttribute( 'created', 21213423 );
        $comment->setAttribute( 'modified', 21321231 );
        $comment->setAttribute( 'user_id', 15 );
        $comment->setAttribute( 'session_key', 'a2e4822a98337283e39f7b60acf85ec9' );
        $comment->setAttribute( 'ip', '10.0.2.122' );
        $comment->setAttribute( 'name', 'xc' );
        $comment->setAttribute( 'email', 'xc@ez.no' );
        $comment->setAttribute( 'url', 'http://ez.no' );
        $comment->setAttribute( 'status', 1 );
        $comment->setAttribute( 'text', 'ezcomComment object test comment.' );
        $comment->setAttribute( 'notification', 0 );
        $comment->store();
        
        // Assert fetchForUser( userid )
        $list = ezcomComment::fetchForUser( 15 );
        $this->assertType( 'array', $list );
        $this->assertType( 'ezcomComment', $list[0] );
        $this->assertEquals( 13, $list[0]->attribute( 'contentobject_id' ) );
        
        // Assert fetchForUser( userid, notification )
        $list = ezcomComment::fetchForUser( 15, 0 );
        $this->assertType( 'array', $list );
        $this->assertType( 'ezcomComment', $list[0] );
        $this->assertEquals( 13, $list[0]->attribute( 'contentobject_id' ) );
        
        // Assert fetchForUser( userid,notification, status )
        $list = ezcomComment::fetchForUser( 15, null, null, null, false, 1 );
        $this->assertType( 'array', $list );
        $this->assertType( 'ezcomComment', $list[0] );
        $this->assertEquals( 13, $list[0]->attribute( 'contentobject_id' ) );
        
        // Assert fetchForUser( userid ) when there is no record in database
        $list = ezcomComment::fetchForUser( 16 );
        $this->assertType( 'array', $list );
        $this->assertSame( 0, count( $list ) );
    }
    
    /**
     * Test the validateInput method in ezcomComment
     * to do: test the localized error
     * @return 
     */
    public function testValidateInput()
    {
        $result = ezcomComment::validateInput( null );
        $this->assertSame( 'Parameter is empty!', $result );
        $comment = ezcomComment::create();
        $result = ezcomComment::validateInput( $comment );
        $this->assertNotSame( 'Parameter is empty!', $result );
        
        //validate name
        $comment->setAttribute( 'name', '' );
        $result = ezcomComment::validateInput( $comment );
        $this->assertSame( 'Name is empty!', $result );
        
        $comment->setAttribute( 'name', 'chen' );
        $result = ezcomComment::validateInput( $comment );
        $this->assertNotSame( 'Name is empty!', $result );
        
        //validate email
        $comment->setAttribute( 'email', '' );
        $result = ezcomComment::validateInput( $comment );
        $this->assertSame( 'Email is empty!', $result );
        
        $comment->setAttribute( 'email', 'xc2357fddf' );
        $result = ezcomComment::validateInput( $comment );
        $this->assertSame( 'Not a valid email address!', $result );
        
        $comment->setAttribute( 'email', 'xc@ez.no' );
        $result = ezcomComment::validateInput( $comment );
        $this->assertNotSame( 'Email is empty!', $result );
        $this->assertNotSame( 'Not a valid email address!', $result );
        
        // validate text
        $comment->setAttribute( 'text', '' );
        $result = ezcomComment::validateInput( $comment );
        $this->assertSame( 'Content is empty!', $result );
        
        $comment->setAttribute( 'text', 'test comment:)))' );
        $result = ezcomComment::validateInput( $comment );
        $this->assertNotEquals( 'Content is empty!', $result );
        
        // validate language_id
        $comment->setAttribute( 'language_id', '' );
        $result = ezcomComment::validateInput( $comment );
        $this->assertSame( 'Language is empty or not int!', $result );
        
        $comment->setAttribute( 'language_id', 'dd' );
        $result = ezcomComment::validateInput( $comment );
        $this->assertSame( 'Language is empty or not int!', $result );
        
        $comment->setAttribute( 'language_id', 2 );
        $result = ezcomComment::validateInput( $comment );
        $this->assertNotSame( 'Language is empty or not int!', $result );
        
        // validate contentobject_id
        $comment->setAttribute( 'contentobject_id', '' );
        $result = ezcomComment::validateInput( $comment );
        $this->assertSame( 'Object ID can not be empty or string!', $result );
        
        $comment->setAttribute( 'contentobject_id', 'ss' );
        $result = ezcomComment::validateInput( $comment );
        $this->assertSame( 'Object ID can not be empty or string!', $result );
        
        $comment->setAttribute( 'contentobject_id', 12 );
        $result = ezcomComment::validateInput( $comment );
        $this->assertNotSame( 'Object ID can not be empty or string!', $result );
        
        // validate all
        $this->assertTrue( $result );
    }
    
    /**
     * Test the addcomment method in ezcomComment
     */
    public function testAddComment()
    {
        //1. test adding a comment without notification
        $input = array();
        $input['name'] = 'xc';
        $input['email'] = 'xc@ez.no';
        $input['text'] = 'This is a test comment:)';
        $input['notified'] = false;
        $user = eZUser::currentUser();
        $contentObjectID = 209;
        $languageID = 2;
        $time = time();
        //1.1 without subscription
        $result = ezcomComment::addComment( $input, $user, $contentObjectID, $languageID, $time );
        $this->assertTrue( $result['result'] );
        $comment = ezcomComment::fetchByTime( 'created', $time );
        $this->assertEquals( $input['text'], $comment->attribute( 'text' ) );
        $this->assertEquals( $input['name'], $comment->attribute( 'name' ) );
        $this->assertEquals( $input['notified'], $comment->attribute( 'notified' ) );
        
        //1.2 with subscription
        //add subscriber
        $time = $time + 1;
        $subscriber = ezcomSubscriber::create();
        $subscriber->setAttribute( 'user_id', $user->attribute( 'contentobject_id' ) );
        $subscriber->setAttribute( 'email', $input['email'] );
        $subscriber->store();
        //add subscription
        $subscription = ezcomSubscription::create();
        $subscription->setAttribute( 'subscriber_id', $subscriber->attribute( 'id' ) );
        $subscription->setAttribute( 'content_id', $contentObjectID . '_' . $languageID );
        $subscription->setAttribute( 'user_id', $user->attribute( 'contentobject_id' ) );
        $subscription->setAttribute( 'subscription_type', 'ezcomcomment' );
        $subscription->setAttribute( 'subscription_time', $time );
        $subscription->store();
        //add comment
        $result = ezcomComment::addComment( $input, $user, $contentObjectID, $languageID, $time );
        $comment = ezcomComment::fetchByTime( 'created', $time );
        $this->assertEquals( $input['text'], $comment->attribute( 'text' ) );
        $this->assertEquals( $input['name'], $comment->attribute( 'name' ) );
        $this->assertEquals( $input['notified'], $comment->attribute( 'notified' ) );
        $this->assertTrue( $result['result'] );
        //vertify the notification
        $notifications = ezcomNotification::fetchNotificationList( 1, 1, 0, array( 'id' => 'desc' ) );
        $this->assertEquals( $contentObjectID, $notifications[0]->attribute( 'contentobject_id' ) );
        $this->assertEquals( $comment->attribute( 'id' ), $notifications[0]->attribute( 'comment_id' ) );
        $this->assertEquals( $languageID, $notifications[0]->attribute( 'language_id' ) );
        
        //2. test adding a comment with notification
        $input2 = array();
        $input2['name'] = 'chen';
        $input2['email'] = 'cxj2007@gmail.com';
        $input2['text'] = 'notified comment';
        $input2['notified'] = false;
        $user2 = eZUser::currentUser();
        $contentObjectID = 210;
        $languageID = 2;
        //2.1 if there is no subscription
        $time2 = time() + 3;
        $result2 = ezcomComment::addComment( $input2, $user2, $contentObjectID, $languageID, $time2 );
        $this->assertTrue( $result2['result'] );
        $comment = ezcomComment::fetchByTime( 'created', $time2 );
        $this->assertEquals( $input2['text'], $comment->attribute( 'text' ) );
        $this->assertEquals( $input2['name'], $comment->attribute( 'name' ) );
        $this->assertEquals( $input2['notified'], $comment->attribute( 'notified' ) );
        $notifications = ezcomNotification::fetchNotificationList( 1, 1, 0, array( 'id' => 'desc' ) );
        $this->assertNotEquals( $notifications[0]->attribute( 'comment_id' ), $comment->attribute( 'id' ) ); //assert that there is no new notification
        //2.2 if there is already subscription
        $time2 = $time2 + 1;
        //add subscriber
        $subscriber = ezcomSubscriber::create();
        $subscriber->setAttribute( 'user_id', $user2->attribute( 'contentobject_id' ) );
        $subscriber->setAttribute( 'email', $input['email'] );
        $subscriber->store();
        //add subscription
        $subscription = ezcomSubscription::create();
        $subscription->setAttribute( 'subscriber_id', $subscriber->attribute( 'id' ) );
        $subscription->setAttribute( 'content_id', $contentObjectID . '_' . $languageID );
        $subscription->setAttribute( 'user_id', $user2->attribute( 'contentobject_id' ) );
        $subscription->setAttribute( 'subscription_type', 'ezcomcomment' );
        $subscription->setAttribute( 'subscription_time', $time2 );
        $subscription->store();
        //add comment
        $result = ezcomComment::addComment( $input2, $user2, $contentObjectID, $languageID, $time2 );
        $comment = ezcomComment::fetchByTime( 'created', $time2 );
        $this->assertEquals( $input2['text'], $comment->attribute( 'text' ) );
        $this->assertEquals( $input2['name'], $comment->attribute( 'name' ) );
        $this->assertEquals( $input2['notified'], $comment->attribute( 'notified' ) );
        $this->assertTrue( $result['result'] );
        //vertify the notification
        $notifications = ezcomNotification::fetchNotificationList( 1, 1, 0, array( 'id' => 'desc' ) );
        $this->assertEquals( $contentObjectID, $notifications[0]->attribute( 'contentobject_id' ) );
        $this->assertEquals( $comment->attribute( 'id' ), $notifications[0]->attribute( 'comment_id' ) );
        $this->assertEquals( $languageID, $notifications[0]->attribute( 'language_id' ) );
    }
    
    /**
     * Test updateComment method
     * Test cases:
     *      1) update title, website, text
     *         the title, name, website, text will be updated
     *      2) update notified to be true
     *         if there is subscription for the user and content, nothing changed
     *         if there is no subscription for the user and content, add one subscription
     *         there will be a new notification in notification queue.
     *      3) update notified to be false
     *         if there is no notified for the user and content, delete subscription
     *         if there is still notified for the user and content, keep the subscription 
     */
    public function testUpdateComment()
    {
        //add comment&subscription
        $input = array();
        $input['name'] = 'xc';
        $input['email'] = 'xcccccc@ez.no';
        $input['text'] = 'This is a test comment for updating:)';
        $input['notified'] = false;
        $user = eZUser::currentUser();
        $contentObjectID = 219;
        $languageID = 3;
        $time = time() + 9;
        $result = ezcomComment::addComment( $input, $user, $contentObjectID, $languageID, $time );
        $comment = ezcomComment::fetchByTime( 'created', $time );
        
        // 1. update title, name, website, text
        $commentInput = array();
        $commentInput['title'] = 'title changed:)';
        $commentInput['text'] = 'text \' changed?11';
        $commentInput['url'] = 'http://dfsfsdf.com';
        $updateResult = ezcomComment::updateComment( $commentInput, $comment->attribute( 'id' ), $user );
        $this->assertTrue( $updateResult );
        $updatedComment = ezcomComment::fetchByTime( 'created', $time );
        $this->assertEquals( $commentInput['title'], $updatedComment->attribute( 'title' ) );
        $this->assertEquals( $commentInput['text'], $updatedComment->attribute( 'text' ) );
        $this->assertEquals( $commentInput['url'], $updatedComment->attribute( 'url' ) );
        $this->assertFalse( ezcomComment::updateComment( null, 1, null ) );
        
        $subscriber = ezcomSubscriber::fetchByEmail( $input['email'] );
        $this->assertNull( $subscriber );
        
        $hasSubscription = ezcomSubscription::exists( $contentObjectID . '_' . $languageID, 
                                            'ezcomcomment', $input['email'] );
        $this->assertFalse( $hasSubscription );
        
        // 2. update notified true
        $commentInput =array();
        $commentInput['notified'] = true;
        $updateResult = ezcomComment::updateComment( $commentInput, $comment->attribute( 'id' ), $user );
        $this->assertTrue( $updateResult );
        $subscriber = ezcomSubscriber::fetchByEmail( $input['email'] );
        $this->assertNotNull( $subscriber );
        $hasSubscription = ezcomSubscription::exists( $contentObjectID . '_' . $languageID, 
                                            'ezcomcomment', $input['email'] );
        $this->assertTrue( $hasSubscription );
        
        //3. update notfied false, the subscriber will be kept, the subscription will be deleted
        $commentInput['notified'] = false;
        $updateResult = ezcomComment::updateComment( $commentInput, $comment->attribute( 'id' ), $user );
        $subscriber = ezcomSubscriber::fetchByEmail( $input['email'] );
        $this->assertNotNull( $subscriber );
        $hasSubscription = ezcomSubscription::exists( $contentObjectID . '_' . $languageID, 
                                            'ezcomcomment', $input['email'] );
        $this->assertFalse( $hasSubscription );
    }
    
    // todo: finished the test case
    public function testAddSubscription()
    {
        
    }
    
    public function testDeleteCommentWithSubscription()
    {
        // 1.1 create comment and subscription
        $input = array();
        $input['name'] = 'xc';
        $input['email'] = 'ccccc@ez.no';
        $input['text'] = 'This is a test comment for deleting!';
        $input['notified'] = true;
        $user = eZUser::currentUser();
        $time = time() + 10;
        $contentObjectID = 222;
        $languageID = 3;
        ezcomComment::addComment( $input, $user, $contentObjectID, $languageID, $time );
        $comment = ezcomComment::fetchByTime( 'created', $time );
        $commentID = $comment->attribute( 'id' );
        // 1.2 delete the comment
        $this->assertTrue( ezcomSubscription::exists( $contentObjectID . '_' . $languageID, 'ezcomcomment', $input['email'] ) );
        ezcomComment::deleteCommentWithSubscription( $commentID );
        $this->assertNull( ezcomComment::fetch( $commentID ) );
        $this->assertFalse( ezcomSubscription::exists( $contentObjectID . '_' . $languageID, 'ezcomcomment', $input['email'] ) );
        // 2.1 create comment without subscription
        $input['notified'] = false;
        ezcomComment::addComment( $input, $user, $contentObjectID, $languageID, $time );
        $comment = ezcomComment::fetchByTime( 'created', $time );
        $commentID = $comment->attribute( 'id' );
        // 2.2 delete the comment
        $this->assertFalse( ezcomSubscription::exists( $contentObjectID . '_' . $languageID, 'ezcomcomment', $input['email'] ) );
        ezcomComment::deleteCommentWithSubscription( $commentID );
        $this->assertNull( ezcomComment::fetch( $commentID ) );
    }
}
?>