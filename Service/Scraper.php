<?php

namespace Service;

use DOMDocument;
use DOMXPath;

class Scraper
{
    private $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function scrape()
    {
        $html = file_get_contents($this->url);
        $dom = new DOMDocument();

        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        
        $entries = $xpath->query("//div[contains(@class, 'sl_list_inner')]");
        $data = [];
        
        foreach ($entries as $entry) {
            $company_name = '';
            $ticker_symbol = '';
            $settlement_fund = '';
            
            $h5_elements = $xpath->query(".//h5[contains(@class, 'primary_color font_w7')]", $entry);
            
            foreach ($h5_elements as $h5) {
                $text = trim($h5->nodeValue);
                
                if (strpos($text, 'Settlement Fund') === false) {
                    if (preg_match('/\(([^)]+)\)$/', $text, $companyAndTicker)) {
                        $ticker_symbol = $companyAndTicker[1]; // Get text inside the last parentheses
                        $company_name = trim(substr($text, 0, strrpos($text, "($ticker_symbol)")));
                    } else {
                        $company_name = $text;
                        $ticker_symbol = "N/A";
                    }
                } else {
                    $settlement_fund = $text;
                }
            }

            $deadline = trim(string: $xpath->evaluate("string(.//p[span[contains(text(), 'Deadline:')]])", $entry));
            $settlement_hearing_date = trim($xpath->evaluate("string(.//p[span[contains(text(), 'Settlement Hearing Date:')]])", $entry));
            $class_period = trim($xpath->evaluate("string(.//p[span[contains(text(), 'Class Period:')]])", $entry));
            $post_url = trim($xpath->evaluate("string(.//a[contains(text(), 'Learn More')]/@href)", $entry));
            
            $deadline = preg_replace('/Deadline:\s*/', '', $deadline);
            $settlement_hearing_date = preg_replace('/Settlement Hearing Date:\s*/', '', $settlement_hearing_date);
            $class_period = preg_replace('/Class Period:\s*/', '', $class_period);
            $class_period_data = explode('-', $class_period);

            $settlement_fund = (int) str_replace(['$', ','], '', preg_replace('/Settlement Fund:\s*/', '', $settlement_fund));
            
            $data[] = [
                'company_name' => $company_name,
                'ticker_symbol' => $ticker_symbol,
                'settlement_fund' => $settlement_fund,
                'deadline' => date('Y-m-d', strtotime($deadline)),
                'settlement_hearing_date' => date('Y-m-d', strtotime($settlement_hearing_date)),
                'class_period_start' => date('Y-m-d', strtotime($class_period_data[0])),
                'class_period_end' => date('Y-m-d', strtotime($class_period_data[1])),
                'post_url' => $post_url
            ];
        }

        return $data;
    }
}