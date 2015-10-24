<?php
namespace PHPVCSControl\Git;

use PHPVCSControl\CommitInterface;

class Commit implements CommitInterface
{
    public $sha;
    public $name;
    public $email;
    public $date;
    public $subject;
    public $changes;
    public $linesAdded = 0;
    public $linesRemoved = 0;

    public function __construct($sha, $name, $email, $date, $subject, $changes)
    {
        $this->sha = $sha;
        $this->name = $name;
        $this->email = $email;
        $this->date = $date;
        $this->subject = $subject;
        $this->changes = $changes;

        foreach ($changes as $change) {
            $this->linesAdded += $change['+'];
            $this->linesRemoved += $change['-'];
        }
    }

    public static function build(array $info)
    {
        $sha = $info['commit'];
        $name = $info['committer_name'];
        $email = $info['committer_email'];
        $date = $info['committer_date'];
        $subject = $info['subject'];
        $changes = $info['changes'];

        return new self($sha, $name, $email, $date, $subject, $changes);
    }
}
