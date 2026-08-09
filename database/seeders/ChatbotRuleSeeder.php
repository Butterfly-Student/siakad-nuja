<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ChatbotRule;
use Illuminate\Database\Seeder;

class ChatbotRuleSeeder extends Seeder
{
    public function run(): void
    {
        $defaultRules = [
            [
                'keyword'     => '1',
                'judul_menu'  => '📊 Info Nilai Rapor',
                'tipe_action' => 'system_query',
                'action_key'  => 'info_nilai',
                'isi_balasan' => null,
                'urutan'      => 1,
                'is_active'   => true,
            ],
            [
                'keyword'     => '2',
                'judul_menu'  => '📋 Info Rekap Kehadiran',
                'tipe_action' => 'system_query',
                'action_key'  => 'info_kehadiran',
                'isi_balasan' => null,
                'urutan'      => 2,
                'is_active'   => true,
            ],
            [
                'keyword'     => '3',
                'judul_menu'  => '💳 Info Tagihan & Pembayaran',
                'tipe_action' => 'system_query',
                'action_key'  => 'info_tagihan',
                'isi_balasan' => null,
                'urutan'      => 3,
                'is_active'   => true,
            ],
            [
                'keyword'     => '4',
                'judul_menu'  => '📢 Info Agenda Sekolah Terbaru',
                'tipe_action' => 'system_query',
                'action_key'  => 'info_agenda',
                'isi_balasan' => null,
                'urutan'      => 4,
                'is_active'   => true,
            ],
            [
                'keyword'     => '5',
                'judul_menu'  => '📞 Hubungi Customer Service',
                'tipe_action' => 'system_query',
                'action_key'  => 'cs_contact',
                'isi_balasan' => null,
                'urutan'      => 5,
                'is_active'   => true,
            ],
        ];

        foreach ($defaultRules as $rule) {
            ChatbotRule::updateOrCreate(
                ['keyword' => $rule['keyword']],
                $rule
            );
        }
    }
}
