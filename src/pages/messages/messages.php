    <?php
    //db connection and helper file
    include "../db_connection.php";
    include "../helperFunctions.php";

    setupHeader();

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Make sure user is logged in
    if (!isset($_SESSION['user_id'])) {
        // Redirect to login page or error page
        header('Location: login.php');
        exit;
    }

    // Set user ID
    $userId = $_SESSION['user_id'];

    // Initialise variables for the matchId messages and conversations
    $matchId = null;
    $messages = [];
    $allConversations = [];

    // if match id is not set, get all conversations for the user. else get messages for the match id that is set 
    if (!isset($_GET['match_id'])) {
        $allConversations = getMessages($userId);
    } else {
        $matchId = $_GET['match_id'];
        $matchName = getNameByMatchId($matchId, $userId);
        $messages = getMessagesByMatchId($matchId, $userId);
    }


    // Check if the form has been submitted and the message content is set 
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_content'])) {
        $messageContent = $_POST['message_content'];
        // Call function to send message from helper class
        sendMessage($userId, $matchId, $messageContent);
        // reload page so you can display the messages
        header('Location: messages.php?match_id=' . $matchId);
        exit;
    }
    ?>

    <!-- HTML for the messages page -->

    <!DOCTYPE html>
    <html lang="en">

    <head>

        <!-- jQuery api -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

        <link rel="stylesheet" type="text/css" href="../../assets/css/messages.css">

        <!-- Define the userId JavaScript variable -->
        <script type="text/javascript">
            var userId = <?php echo json_encode($userId); ?>;
        </script>
    </head>

    <body>
        <div class="messages-container d-flex">
            <!-- Sidebar for The messages -->
            <div class="sidebar" style="width: 25%;">
                <ul id="conversation-list" class="list-unstyled">
                    <?php foreach ($allConversations as $conversationMatchId) : ?>
                        <?php $matchName = getNameByMatchId($conversationMatchId, $userId); ?>
                        <?php $profilePic = getProfilePictureByMatchId($conversationMatchId, $userId); ?>
                        <img src="/<?php echo htmlspecialchars($profilePic); ?> " class="profile-picture" alt="Profile Picture">
                        <li onclick="loadMessages(<?php echo $conversationMatchId; ?>)">
                            <?php echo htmlspecialchars($matchName); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <!-- area for displaying messages -->
            <div class="content" style="width: 75%;">
                <h2 id="message-title">Messages Displayed here</h2>
                <div id="message-content" class="messages">
                </div>
                <div id="message-form" style="display: none;">
                    <form id="send-message-form" method="post">
                        <textarea name="message_content" id="message_content" required></textarea>
                        <button type="submit">Send Message</button>
                    </form>
                </div>
            </div>
        </div>


        <script>
            var currentMatchId = null;

            function loadMessages(matchId) {
                currentMatchId = matchId;
                // AJAX call to get messages for the specific matchId
                $.ajax({
                    url: 'getMessages.php',
                    type: 'GET',
                    data: {
                        'match_id': matchId
                    },
                    dataType: 'json',
                    success: function(response) {
                        var messagesHtml = '';
                        var matchName = response.matchName;
                        response.messages.forEach(function(message) {
                            var messageClass = message.from_self ? 'my-message' : 'other-message';
                            var unsendMessageButton = message.from_self ?
                                '<button class="unsend-button" onclick="unsendMessage(' + message.message_id + ')">Unsend</button>' :
                                '';
                            messagesHtml += '<div class="message ' + messageClass + '" data-message-id="' + message.message_id + '">' +
                                message.message_content + unsendMessageButton + '</div>';
                        });
                        // Set the HTML of the message-content div
                        $('#message-content').html(messagesHtml);
                        $('#message-title').text(matchName);
                        $('#message-form').show();
                    }
                });
            }

            // event handler for form submission.
            $('#send-message-form').submit(function(e) {
                //Prevents the default action of the form submission
                e.preventDefault();
                //gets message content from the textArea of the form
                var messageContent = $('#message_content').val();
                //if theres message content and a current match id, send the message by calling sendMessage.php
                if (messageContent && currentMatchId) {
                    $.ajax({
                        url: 'sendMessage.php',
                        type: 'POST',
                        data: {
                            //message content main data (match id is temp)
                            'message_content': messageContent,
                            'match_id': currentMatchId
                        },
                        success: function(response) {
                            if (response.success) {
                                loadMessages(currentMatchId);
                                $('#message_content').val('');
                            } else {
                                alert('Error: ' + response.error);
                            }
                        }
                    });
                }
            });

            //function for unsending message
            function unsendMessage(messageId) {
                //AJAX call to deleteMessage.php to delete the message    
                $.ajax({
                    url: 'deleteMessage.php',
                    type: 'POST',
                    data: {
                        'message_id': messageId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Remove the message element from the display
                            $('div[data-message-id="' + messageId + '"]').remove();
                        } else {
                            // Hgive the error
                            alert('Error: ' + response.error);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // display an error message
                        alert('AJAX error: ' + textStatus + ', ' + errorThrown);
                    }
                });
            }
        </script>

        <?php setupFooter(); ?>
    </body>

    </html>