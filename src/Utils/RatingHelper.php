<?php

namespace Utils;

class RatingHelper
{

    public static function getRatingStars(float $averageRating): string
    {

        $starsHtml = '<div class="stars-container">';

        for ($score = 1; $score <= 5; $score++)
        {
            $fill = 0;

            if ($averageRating >= $score)
            {
                $fill = 100;
            }
            elseif ($averageRating > ($score - 1))
            {
                $fill = ($averageRating - ($score - 1)) * 100;
            }

            $starsHtml .= 
            '
                <div class="star">
                    <div class="star-empty">&#9733;</div>
                    <div class="star-filled" style="width: '.$fill.'%">&#9733;</div>
                </div>
            ';
        }

        $starsHtml .= '</div>';

        return $starsHtml;

    }

    public static function getRatingText(float $averageRating, int $totalReviews): string
    {

        if ($totalReviews === 0)
        {
            return 'Нет оценок';
        }

        $reviewsText = self::getReviewsText($totalReviews);

        return sprintf('%.1f из 5 (%s)', $averageRating, $reviewsText);

    }

    private static function getReviewsText(int $count): string
    {

        $lastDigit = $count % 10;

        return match($lastDigit) 
        {
            1 => "{$count} оценка",
            2, 3, 4 => "{$count} оценки",
            default => "{$count} оценок"
        };

    }

}