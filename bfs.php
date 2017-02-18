#!/usr/bin/php
<?php

class Graph {

    function __construct($_nodes, $_edges, $_n, $_m, $_nodes_outer, $_edges_outer, $_n_outer, $_m_outer) {
        $this->n = $_n;
        $this->m = $_m;
        $this->nodes = $_nodes;
        $this->edges = $_edges;
        for ($i = 0; $i < $this->m; $i++) {
            $u = $this->edges[$i][0];
            $v = $this->edges[$i][1];
            $this->adj[$u][] = $v;
        }
        
        foreach($this->adj as $u => $adj_list)
        {
            for($i = 1; $i < count($adj_list); $i++)
            {
                $this->siblings[$i] = $adj_list[$i-1];
            }
        }

        $this->n_outer = $_n_outer;
        $this->m_outer = $_m_outer;
        $this->nodes_outer = $_nodes_outer;
        $this->edges_outer = $_edges_outer;
        for ($i = 0; $i < $this->m_outer; $i++) {
            $u = $this->edges_outer[$i][0];
            $v = $this->edges_outer[$i][1];
            $this->adj_outer[$u][] = $v;
        }
    }
    
    public function bfsDepths()
    {
        $this->bfsDepthsAux($this->adj);
    }

    public function bfsDepthsAux($adj)
    {
        $s = new SplQueue();
        $current_depth = 0;
        $s->enqueue([0,$current_depth]);
        
        while(!$s->isEmpty())
        {
            list($u, $d) = $s->dequeue();
            
            $this->depths[$u] = $d;
            
            if($d > $current_depth)
            {
                $current_depth = $d;
            }
            
            if(!isset($adj[$u]))
            {
                continue;
            }
            
            foreach($adj[$u] as $v)
            {
                $s->enqueue([$v, $current_depth+1]);
            }
        }
        
        $this->max_depth = $current_depth;
    }
    
    public function dfsRunner() {
        // initialize cardinality to one
        $this->column_cardinality = [];
        for ($i = 0; $i < count($this->nodes); $i++) {
            $this->column_cardinality[$i] = 0;
        }

        // run dfs
        foreach ($this->adj[0] as $v) {
            $this->column_cardinality[0] += $this->dfs($v);
        }

        // filter offset
        for ($i = 0; $i < count($this->nodes); $i++) {
            if ($this->column_cardinality[$i] == 0) {
                $this->column_cardinality[$i] = 1;
            }
        }
               
        // append empty leaves to columns with cardinality one
        for ($i = 1; $i < count($this->nodes); $i++) {
            if ($this->column_cardinality[$i-1] == 1 && $this->depths[$i-1] < $this->max_depth) {
                $this->adj[$i-1][] = '-1';
            }
        }
        
        $this->column_cardinality['-1'] = 1;
    }

    public function dfs($node) {
        if (!isset($this->adj[$node])) {
            return 1;
        }

        // run dfs
        foreach ($this->adj[$node] as $v) {
            $this->column_cardinality[$node] += $this->dfs($v);
        }

        return $this->column_cardinality[$node];
    }

    public function bfs() {
        $q = new SplQueue();
        $q->enqueue([0,-1,-1]);
        $current_level = -1;

        echo "<tr>";

        while (!$q->isEmpty()) {
            list($u,$p,$level) = $q->dequeue();

            // close raw
            if ($level != $current_level) {
                // level has advanced
                echo "</tr><tr>";
                $current_level++;
            }
            
            // print column
            echo sprintf('<td colspan="%d">%s</td>', $this->column_cardinality[$u], $this->nodes[$u]);

            if ($u == '-1' || !isset($this->adj[$u])) {
                continue;
            }

            // relax next level
            foreach ($this->adj[$u] as $v) {
                $q->enqueue([$v,$u,$current_level + 1]);
            }
        }

        echo "</tr>";
    }

    public function bfsOuter() {
        $q = new SplQueue();
        $q->enqueue([0, -1]);
        $current_level = -1;

        echo "<tr>";

        while (!$q->isEmpty()) {
            list($u, $level) = $q->dequeue();

            // close raw
            if ($level != $current_level) {
                // level has advanced
                echo "</tr><tr>";
                $current_level++;
            }

            // print column
            $number_of_child_nodes = 1;
            if (isset($this->adj[$u])) {
                $number_of_child_nodes = count($this->adj[$u]);
            }

            echo sprintf('<td colspan="%d">%s</td>', $number_of_child_nodes, $this->nodes[$u]);

            if (!isset($this->adj[$u])) {
                continue;
            }

            // relax next level
            foreach ($this->adj[$u] as $v) {
                $q->enqueue([$v, $current_level + 1]);
            }
        }

        echo "</tr>";
    }

    private $nodes;
    private $edges;
    private $adj;
    private $siblings;
    private $n;
    private $m;
    private $depths;
    private $max_depth;
    private $columns_cardinality;
    
    private $nodes_outer;
    private $edges_outer;
    private $adj_outer;
    private $siblings_outer;
    private $n_outer;
    private $m_outer;
    private $depths_outer;
    private $max_depth_outer;
    private $rows_cardinality;

}

$nodes = [
    '-1'=> '',
    0 => 'Arabic Countries',
    1 => 'Egypt',
    2 => 'Saudi Arabia',
    3 => 'Algeria',
    4 => 'Egypt Public Sector',
    5 => 'Egypt Private Sector',
    6 => 'Egypt Both Sectors',
    7 => 'Egypt Public Sector Males',
    8 => 'Egypt Public Sector Females',
    9 => 'Egypt Public Sector Both Genders',
    10 => 'Saudi Arabia Public Sector',
    11 => 'Saudi Arabia Private Sector',
    12 => 'Saudi Arabia Both Sectors',
    13 => 'Saudi Arabia Public Sector Males',
    14 => 'Saudi Arabia Public Sector Females',
    15 => 'Saudi Arabia Public Sector Both Genders',
    16 => 'Algeria Public Sector',
    17 => 'Algeria Private Sector',
    18 => 'Algeria Both Sectors',
    19 => 'Algeria Public Sector Males',
    20 => 'Algeria Public Sector Females',
    21 => 'Algeria Public Sector Both Genders'
];

$edges = [
    [0, 1],
    [0, 2],
    [0, 3],
    [1, 4],
    [1, 5],
    [1, 6],
    [2, 10],
    [2, 11],
    [2, 12],
    [3, 16],
    [3, 17],
    [3, 18],
    [4, 7],
    [4, 8],
    [4, 9],
    [10, 13],
    [10, 14],
    [10, 15],
    [16, 19],
    [16, 20],
    [16, 21]
];

$nodes_outer = [
    0 => 'High Education',
    1 => 'High School',
    2 => 'BSC',
    3 => 'MSC',
    4 => 'PHD',
    5 => 'Technical',
    6 => 'Commercial',
    7 => 'Secondary'
];

$edges_outer = [
    [0, 2],
    [0, 3],
    [0, 4],
    [1, 5],
    [1, 6],
    [1, 7]
];

$g = (new ReflectionClass('Graph'))->newInstanceArgs([$nodes, $edges, 22, 21, $nodes_outer, $edges_outer, 8, 6]);
$g->bfsDepths();
$g->dfsRunner();
$g->bfs();
//$g->bfsOuter();