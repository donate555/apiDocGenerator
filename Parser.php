<?php
class Parser {

    public function fromFile ($file) {
        $content = file_get_contents($file);
        $comments = $this->getComments($content);
        $doc = [];
        foreach ($comments as $comment) {
            if (empty($comment) || preg_match("/\*\ \@api/", $comment) == 0) {
                continue;
            }
            $doc[] = [
                'params' => $this->getParams($comment),
                'columns' => $this->getColumns($comment),
                'url' => $this->getField($comment, 'url'),
                'name' => $this->getField($comment, 'name'),
                'desc' => $this->getField($comment, 'desc'),
                'method' => $this->getField($comment, 'method'),
            ];
        }
        return $doc;
    }

    public function getComments ($str) {
        $matches = [];
        preg_match_all('/\/\*([^\*^\/]*|[\*^\/*]*|[^\**\/]*)*\*\//', $str, $matches);
        return $matches[0];
    }

    public function getField ($str, $field) {
        $matches = [];
        preg_match("/\*\ \@{$field}\ (.*)/", $str, $matches);
        return $matches[1];
    }

    public function getFields ($str, $field) {
        $matches = [];
        preg_match_all("/\*\ \@{$field}\ *(.*)/", $str, $matches);
        return $matches[1];
    }

    public function getParams ($str) {
        $params = [];
        $matches = $this->getFields($str, 'param');
        foreach ($matches as $match) {
            $f = array_merge(array_filter(explode(' ', $match)));
            $params[] = [
                'name' => $f[0],
                'class' => $f[1],
                'needle' => $f[2],
                'desc' => join(' ', array_slice($f, 3)),
            ];
        }
        return $params;
    }

    public function getColumns ($str) {
        $rets = [];
        $matches = $this->getFields($str, 'column');
        foreach ($matches as $match) {
            $f = array_merge(array_filter(explode(' ', $match)));
            $rets[] = [
                'name' => $f[0],
                'desc' => join(' ', array_slice($f, 1)),
            ];
        }
        return $rets;
    }

}