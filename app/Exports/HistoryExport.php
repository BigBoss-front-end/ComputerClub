<?php

namespace App\Exports;

use App\Models\History;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HistoryExport implements FromCollection, WithHeadings
{
    protected $histories;

    public function __construct($histories) {
        $this->histories = $histories;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->histories->map(function($history) {
            return [
                'date' => Carbon::createFromFormat('Y-m-d H:i:s', $history->date)->format('d.m.Y H:i'),
                'name' => $history->position->name,
                'is_revision' => $history->is_revision ? 'Да' : 'Нет',
                'count' => $history->count > 0 ? '+'.$history->count : $history->count,
                'user' => $history->user->name,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Дата',
            'Название',
            'Ревизия?',
            'Количество',
            'Пользователь',
        ];
    }
}
