<?php
require_once './vendor/autoload.php';
use BigBlueButton\BigBlueButton;
use BigBlueButton\Parameters\GetMeetingInfoParameters;
use BigBlueButton\Parameters\CreateMeetingParameters;
use BigBlueButton\Parameters\SetConfigXMLParameters;
use BigBlueButton\Parameters\JoinMeetingParameters;
use BigBlueButton\Response\CreateMeetingResponse;

$bbb = new BigBlueButton();
$response = $bbb->getMeetings();
$meeting_count = 0;
$rand =  substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 5);
(isset($_POST['username'])) ? $username = $_POST['username'] : $username = "test";
$meeting_id = "";

if ($response->getReturnCode() == 'SUCCESS') {

        // searching the breakout-rooms
        $meetings = $response->getMeetings();
        foreach ($meetings as $meeting) {
                if (substr($meeting->getMeetingID(), 0, 7) == "PRE") {
                        $mymeetings[] = $meeting;
                        $meeting_count++;
                }
        }       if ($meeting_count > 0) {
                foreach($mymeetings as $mymeeting) {

                        // if there are nowhere one lonely person in a room, create room

                        if ($mymeeting->getParticipantCount() == 1) { $meeting_id = $mymeeting->getMeetingID(); }
                }
        }
        if ($meeting_id == "") {
                $meeting_id = "PRE".$rand;
                $createParams = new CreateMeetingParameters($meeting_id, "Speeddating online,..");
                $createParams->setModeratorPassword("passwd");
                $createParams->setAttendeePassword("passwd");
                $createParams->setWelcomeMessage("<p>Welcome to Speed-Dating. You have only seven minutes, hurry up!</p>");
                $createParams->setLogo("https://default.png");
                $createParams->setDuration(7);
                $createParams->addPresentation("https://default.pdf");
                $createParams->setLogoutUrl("https://default/entrance.html");

                $response = $bbb->createMeeting($createParams);
        }

        // create joining parameters
        $joinParams = new JoinMeetingParameters($meeting_id, $username, "my dear");
        $joinParams->setRedirect(true);
        $joinParams->setJoinViaHtml5(true);

        header('Status: 301 Moved Permanently', false, 301);
        header('Location:' . $bbb->getJoinMeetingURL($joinParams));

} else print("BBB-Server nicht erreichbar\n");
                                                                       


        // there are meetings yet
