<?php
// -----
// Part of the "Search: Prioritize Matching Names" plugin for Zen Cart v2.0.0 and later
// Copyright (C) 2015-2024, Vinos de Frutas Tropicales (lat9)
//
// Last updated: v2.0.0
//
// @license https://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
//
class zcObserverPrioritizeSearchMatchInName extends base
{
    protected string $order_by;

    public function __construct()
    {
        $this->attach(
            $this,
            [
                'NOTIFY_SEARCH_SELECT_STRING',
                'NOTIFY_SEARCH_REAL_ORDERBY_STRING',
            ]
        );
    }

    public function update(&$class, $eventID, $p1, &$p2, &$p3)
    {
        switch ($eventID) {
            // -----
            // From class.search.php
            //
            // $p1 ... (r/o) The current $select_str
            // $p2 ... (r/w) The current $select_str
            //
            case 'NOTIFY_SEARCH_SELECT_STRING':
                $keywords = $_GET['keyword'] ?? '';
                if (empty($keywords)) {
                    return;
                }

                $in_name_select = zen_build_keyword_where_clause(['pd.products_name'], $keywords);
                $in_name_select = substr($in_name_select, 5);   //- Remove unwanted ' AND (' lead-in
                 if ($in_name_select !== '') {
                    $p2 .= ", IF ($in_name_select, 1, 0) AS in_name ";
                    $this->order_by = 'in_name DESC, ';
                }
                break;

            // -----
            // From class.search.php
            //
            // $p1 ... (r/o) The current $order_str
            // $p2 ... (r/w) The current $order_str
            //
            case 'NOTIFY_SEARCH_REAL_ORDERBY_STRING':
                if (isset($this->order_by)) {
                    $p2 = str_ireplace('order by', 'ORDER BY ' . $this->order_by, $p2);
                }
                break;

            default:
                break;
        }
    }
}
