<?php

declare(strict_types=1);

namespace Tests\Feature\Campus;

class CommunityTest extends CampusTestCase
{
    public function test_matching_club_posts_and_removed_messages(): void
    {
        $junior = $this->student('junior@example.test');
        $senior = $this->student('senior@example.test', 3);
        $this->withSession(['campus_student_id' => $junior])->get('/campus/matches')->assertOk()->assertSee('同じ学部')->assertSee('senior@example.test');
        $this->post('/campus/message', ['recipient_id' => $senior, 'body' => '送信不可'])->assertNotFound();
        $this->get('/campus/messages')->assertNotFound();
        $this->post('/campus/club', ['name' => 'CUO', 'body' => '毎週水曜日に練習しています。'])->assertRedirect('/campus/clubs');
        $this->get('/campus/clubs')->assertOk()->assertSee('CUO');
        $this->get('/campus/profile')->assertOk();
    }
}
