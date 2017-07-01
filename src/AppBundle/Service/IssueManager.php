<?php

namespace AppBundle\Service;

class IssueManager
{
    /**
     * @param $data
     * @param $closedNum
     * @return array
     */
    public function getIssuesInfo($data, $openNum, $closedNum)
    {
        if (isset($data) && is_array($data) && isset($closedNum)) {
            $issue = [];
            foreach ($data as $key => $value){
                $issue[$key]['id'] = $value['id'];
                $issue[$key]['number'] = $value['number'];
                $issue[$key]['title'] = $value['title'];
                $issue[$key]['assignee'] = $value['user']['login'];
                $issue[$key]['open_issues'] = $openNum;
                $issue[$key]['closed_issues'] = $closedNum;
                $issue[$key]['repository_full_name'] = $value['repository']['full_name'];
                $issue[$key]['html_url'] = $value['user']['html_url'];
                $issue[$key]['labels'] = $value['labels'];
                $issue[$key]['comments'] = $value['comments'];
                $issue[$key]['created_at'] = $this->dateDiff(date('Y-m-d H:i:s'), $value['created_at']);

            }
            return $issue;
        }
    }

    /**
     * @param $data
     * @param $comments
     * @return mixed
     */
    public function getSingleIssueInfo($data, $comments)
    {

        if (isset($data) && is_array($data)) {

                $issue['id'] = $data['id'];
                $issue['number'] = $data['number'];
                $issue['title'] = $data['title'];
                $issue['assignee'] = $data['user']['login'];
                $issue['html_url'] = $data['user']['html_url'];
                $issue['labels'] = $data['labels'];
                $issue['comments'] = $data['comments'];
                $issue['created_at'] = $this->dateDiff(date('Y-m-d H:i:s'), $data['created_at']);
                $issue['state'] = $data['state'];

                foreach ($comments as $key => $value){
                    $issue['comment'][$key]['comment_user'] = $value['user']['login'];
                    $issue['comment'][$key]['comment_id'] = $value['user']['id'];
                    $issue['comment'][$key]['comment_avatar'] = $value['user']['avatar_url'];
                    $issue['comment'][$key]['html_url'] = $value['user']['html_url'];
                    $issue['comment'][$key]['comment_body'] = $value['body'];
                    $issue['comment'][$key]['created_at'] = $this->dateDiff(date('Y-m-d H:i:s'), $value['created_at']);
                }
            return $issue;
        }
    }

    function dateDiff($time1, $time2, $precision = 6) {
        // If not numeric then convert texts to unix timestamps
        if (!is_int($time1)) {
            $time1 = strtotime($time1);
        }
        if (!is_int($time2)) {
            $time2 = strtotime($time2);
        }

        // If time1 is bigger than time2
        // Then swap time1 and time2
        if ($time1 > $time2) {
            $ttime = $time1;
            $time1 = $time2;
            $time2 = $ttime;
        }

        // Set up intervals and diffs arrays
        $intervals = array('year','month','day','hour','minute','second');
        $diffs = array();

        // Loop thru all intervals
        foreach ($intervals as $interval) {
            // Create temp time from time1 and interval
            $ttime = strtotime('+1 ' . $interval, $time1);
            // Set initial values
            $add = 1;
            $looped = 0;
            // Loop until temp time is smaller than time2
            while ($time2 >= $ttime) {
                // Create new temp time from time1 and interval
                $add++;
                $ttime = strtotime("+" . $add . " " . $interval, $time1);
                $looped++;
            }

            $time1 = strtotime("+" . $looped . " " . $interval, $time1);
            $diffs[$interval] = $looped;
        }

        $count = 0;
        $times = array();
        // Loop thru all diffs
        foreach ($diffs as $interval => $value) {
            // Break if we have needed precission
            if ($count >= $precision) {
                break;
            }
            // Add value and interval
            // if value is bigger than 0
            if ($value > 0) {
                // Add s if value is not 1
                if ($value != 1) {
                    $interval .= "s";
                }
                // Add value and interval to times array
                $times[] = $value . " " . $interval;
                $count++;
            }
        }

        // Return string with times
        return implode(", ", $times);
    }

}