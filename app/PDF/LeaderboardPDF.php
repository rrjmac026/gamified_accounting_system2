<?php

namespace App\PDF;

use FPDF;

class LeaderboardPDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 18);
        $this->Cell(0, 10, 'Leaderboard Report', 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    public function generateLeaderboard($ranked, $periodType, $generatedAt)
    {
        $this->AliasNbPages();
        $this->AddPage();
        
        // Period and Generation Time
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 10, "Period: $periodType", 0, 1);
        $this->Cell(0, 10, "Generated at: $generatedAt", 0, 1);
        $this->Ln(5);

        // Table Header
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(10, 10, '#', 1);
        $this->Cell(60, 10, 'Student Name', 1);
        $this->Cell(40, 10, 'Course', 1);
        $this->Cell(40, 10, 'XP Earned', 1);
        $this->Cell(40, 10, 'Tasks Done', 1);
        $this->Ln();

        // Table Content
        $this->SetFont('Arial', '', 11);
        $rank = 1;
        foreach ($ranked as $entry) {
            // Calculate row height based on course name length
            $courseLines = $this->getStringLines($entry['Course'], 40);
            $rowHeight = max(10, $courseLines * 5);
            
            // Store current Y position
            $x = $this->GetX();
            $y = $this->GetY();
            
            // Draw cells with same height
            $this->Cell(10, $rowHeight, $rank++, 1, 0, 'L');
            $this->Cell(60, $rowHeight, $entry['Student Name'], 1, 0, 'L');
            
            // MultiCell for course name (wrapping text)
            $this->MultiCell(40, 5, $entry['Course'], 1, 'L');
            
            // Move back to the same row for remaining cells
            $this->SetXY($x + 110, $y);
            $this->Cell(40, $rowHeight, $entry['Total XP'], 1, 0, 'L');
            $this->Cell(40, $rowHeight, $entry['Tasks Completed'], 1, 0, 'L');
            
            $this->Ln();
        }
    }
    
    // Helper function to calculate number of lines for text wrapping
    private function getStringLines($text, $width)
    {
        $lines = 1;
        $length = strlen($text);
        $charWidth = $this->GetStringWidth('A'); // Average character width
        $maxChars = floor($width / $charWidth);
        
        if ($length > $maxChars) {
            $lines = ceil($length / $maxChars);
        }
        
        return $lines;
    }
}