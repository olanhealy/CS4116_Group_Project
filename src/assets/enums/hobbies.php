<?php

enum Hobby: string
{
    case GOLF = "Golf";
    case KARATE = "Karate";
    case SWIMMING = "Swimming";
    case CHESS = "Chess";
    case READING = "Reading";
    case WRITING = "Writing";
    case PAINTING = "Painting";
    case PHOTOGRAPHY = "Photography";
    case MUSIC = "Music";
    case VIDEO_GAMES = "Video Games";
    case TRAVELING = "Traveling";
    case RUNNING = "Running";
    case YOGA = "Yoga";
    case SURFING = "Surfing";
    case SKATEBOARDING = "Skateboarding";
    case SKATING = "Skating";
    case CYCLING = "Cycling";
    case HIKING = "Hiking";
    case CAMPING = "Camping";
    case FISHING = "Fishing";
    case HUNTING = "Hunting";
    case COOKING = "Cooking";
    case BAKING = "Baking";
    case GARDENING = "Gardening";
    case KNITTING = "Knitting";
    case SEWING = "Sewing";
    case GAELIC = "Gaelic";
    case HURLING = "Hurling";
    case RUGBY = "Rugby";
    case SOCCER = "Soccer";
    case BASKETBALL = "Basketball";
    case TENNIS = "Tennis";
    case VOLLEYBALL = "Volleyball";
    case BASEBALL = "Baseball";
    case FOOTBALL = "Football";
    case AMERICAN_FOOTBALL = "American Football";
    case CRICKET = "Cricket";
    case BADMINTON = "Badminton";
    case TABLE_TENNIS = "Table Tennis";
    case GYMNASTICS = "Gymnastics";
    case DANCING = "Dancing";
    case SINGING = "Singing";
    case ACTING = "Acting";
    case MAGIC = "Magic";
    case COMEDY = "Comedy";
    case DRAMA = "Drama";
    case WATCHING_MOVIES = "Watching Movies";
    case WATCHING_TV_SHOWS = "Watching TV Shows";
    case WATCHING_ANIME = "Watching Anime";
    case WATCHING_CARTOONS = "Watching Cartoons";
    case WATCHING_DOCUMENTARIES = "Watching Documentaries";
    case WATCHING_YOUTUBE = "Watching YouTube";
    case WATCHING_NETFLIX = "Watching Netflix";
    case CODING = "Coding";
    case RESEARCHING = "Researching";
    case LEARNING = "Learning";
    case TEACHING = "Teaching";
    case MENTORING = "Mentoring";
    case COACHING = "Coaching";
    case MEDITATION = "Meditation";
    case PRAYING = "Praying";
    case VOLUNTEERING = "Volunteering";

    case OTHER = "Other";



}

$options = '';
foreach (Hobby::cases() as $case) {
    $value = $case->value;
    $selected = (in_array($value, $selectedHobbiesArray)) ? "selected" : "";
    $options .= "<option value=\"$value\" $selected>" . $value . "</option>";
}
?>