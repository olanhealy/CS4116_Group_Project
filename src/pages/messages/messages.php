    <?php
    //db connection and helper file
    include "../helpers/db_connection.php";
    include "../helpers/helperFunctions.php";
    include_once("../admin/adminHelperFunctions.php");

    accessCheck();

    // Make sure user is logged in


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
    setupHeader();
    ?>

    <!-- HTML for the messages page -->

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Messages</title>
        <link rel="icon" href="/ulSinglesSymbolTransparent.ico" type="image/x-icon">

        <!-- jQuery api -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

        <link rel="stylesheet" type="text/css" href="../../assets/css/messages.css">

        <!-- Define the userId JavaScript variable -->
        <script type="text/javascript">
            var userId = <?php echo json_encode($userId); ?>;
            var matchName = <?php echo htmlspecialchars_decode($matchName, ENT_QUOTES); ?>;
        </script>
    </head>

    <body>
        <div class="container-fluid messages-container col-md-12 col-lg-12 col-sm-12 ">
            <div class="row flex-grow-1">
                <!-- Sidebar for The messages -->
                <div class="sidebar col-lg-3 col-md-3 col-sm-12" id="sidebar">
                    <ul id="conversation-list" class="list-unstyled">
                        <?php foreach ($allConversations as $conversationMatchId) : ?>
                            <!-- Get the name and profile picture of the match, also notifications if unread -->
                            <?php $matchName = getNameByMatchId($conversationMatchId, $userId); ?>
                            <?php $profilePic = getProfilePictureByMatchId($conversationMatchId, $userId); ?>
                            <?php $unreadCount = countUnreadMessages($userId, $conversationMatchId); ?>
                            
                            <li onclick="loadMessages(<?php echo $conversationMatchId; ?>)" >
                                <img src="/<?php echo htmlspecialchars($profilePic); ?> " class="profile-picture" alt="Profile Picture">
                                <span class="match-name"><?php echo htmlspecialchars_decode($matchName, ENT_QUOTES); ?> </span>
                                <?php if ($unreadCount > 0): ?>
                                    <!-- Display the unread message count -->
                                    <span class="unread-badge"><?php echo $unreadCount; ?></span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <!-- area for displaying messages -->
                <div class="content col-lg-9 col-md-9 col-sm-12" id="contentArea">
                    <button id="toggleSidebar" class="btn btn-primary chatList-btn" type="button">My Chats</button>
                    <h2 id="message-title">Message one of your matches!!</h2>
                    <div id="message-content" class="messages">
                    </div>
                    <div id="message-form" style="display: none;">
                        <form id="send-message-form" method="post">
                            <textarea name="message_content" id="message_content" required></textarea>
                            <button type="submit" class="svg-button">
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-send" viewBox="0 0 16 16">
                                    <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576zm6.787-8.201L1.591 6.602l4.339 2.76z" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <script>
            $(document).ready(function() {
                // Add click event listener to toggle button
                $('#toggleSidebar').click(function() {
                    $('#sidebar').toggleClass('show');
                    $('#contentArea').toggleClass('show');
                });
            });

            $('#conversation-list li').click(function() {
                $('#sidebar').removeClass('show');
                $('#contentArea').removeClass('show');
            });

            // AJAX poll to get messages every 5 seconds so more like real-time massaging not having to refresh the page
            setInterval(function() {
                if (currentMatchId) {
                    loadMessages(currentMatchId);
                }
            }, 5000);

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
                                '<button class="btn btn-primary unsend-button" onclick="unsendMessage(' + message.message_id + ')">Unsend</button>' :
                                '';
                            messagesHtml += '<div class="message ' + messageClass + '" data-message-id="' + message.message_id + '">' +
                                message.message_content + unsendMessageButton + '</div>';
                        });
                        // Set the HTML of the message-content div
                        $('#message-content').html(messagesHtml);
                        $('#message-title').text(matchName);
                        $('#message-form').show();

                        var messageBody = document.querySelector('#message-content');
                        messageBody.scrollTop = messageBody.scrollHeight - messageBody.clientHeight;
                        $(listItem).removeClass('highlight-unread');
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