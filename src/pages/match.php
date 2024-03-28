<!DOCTYPE html>
<html lang="en">
    <form action="messages.php" method='GET'>
    <a href="messages.php?match_id=<?php echo $matchId?>"> Match ID: <?php echo $matchId; ?> </a> | Me: <?php echo $_SESSION['id'] ?> | Target: " <?php echo $recipientId; ?>
    
</form>
</html>