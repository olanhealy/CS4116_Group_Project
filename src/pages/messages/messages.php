<?php
//db connection and helper file
include "../db_connection.php";
include "../helperFunctions.php";

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Make sure user is logged in
if (!isset($_SESSION['user_id']) ) {
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
    $messages = getMessagesByMatchId($matchId);
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
    <meta charset="UTF-8">
    <title>Messages</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <!-- jQuery api -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="css/main.css">

    <!-- Bootstrap Icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" type="text/css" href="../assets/css/registration.css">
</head>
<body>
    <div class="messages-container d-flex">
        <!-- Sidebar for conversation list -->
        <div class="sidebar" style="width: 25%;">
            <h2>Placeholder: May not need</h2>
            <ul id="conversation-list" class="list-unstyled">
                <?php foreach ($allConversations as $conversationMatchId): ?>
                    <li onclick="loadMessages(<?php echo $conversationMatchId; ?>)">Conversation <?php echo $conversationMatchId; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!--  area for displaying messages -->
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

    
    <!-- JS for loading messages and sending messages  -->
    <script>
    var currentMatchId = null;
 
    function loadMessages(matchId) {
        currentMatchId = matchId;
        // AJAX call for a GET request to getMessages.php to get messages for that specific matchId
        $.ajax({
            url: 'getMessages.php', 
            type: 'GET',
            data: { 'match_id': matchId },
            dataType: 'json',
            success: function(messages) {
                //empty string so can build html for messages
                var messagesHtml = '';
                //loop through all messsages retrieved from getMessages.php being called
                messages.forEach(function(message) {
                  //  @TODO: Frontend, this div class to be designed so ur green and brown etc
                    messagesHtml += '<div class="message">' + 
                                    'From ' + message.sender_id + ': ' + 
                                    message.message_content + '</div>';
                });
                //insers the messages into the message-content div
                $('#message-content').html(messagesHtml);
                //set the title of the messages to be  the matchId (placeholder)
                $('#message-title').text('Message' + matchId);
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
</script>
</body>
</html>