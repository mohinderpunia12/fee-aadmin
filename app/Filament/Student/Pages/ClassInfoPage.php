<?php

namespace App\Filament\Student\Pages;

use Filament\Pages\Page;
use Filament\Infolists;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;

class ClassInfoPage extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static string $view = 'filament.student.pages.class-info';

    protected static ?string $navigationLabel = 'Class Information';

    protected static ?int $navigationSort = 3;

    public function infolist(Infolist $infolist): Infolist
    {
        $user = auth()->user();
        $student = $user->userable;

        return $infolist
            ->record($student)
            ->schema([
                Infolists\Components\Section::make('Classroom Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('classroom.name')
                            ->label('Classroom Name'),
                        Infolists\Components\TextEntry::make('section')
                            ->label('Section'),
                        Infolists\Components\TextEntry::make('classroom.teacher.name')
                            ->label('Class Teacher'),
                        Infolists\Components\TextEntry::make('classroom.capacity')
                            ->label('Class Capacity'),
                    ])
                    ->columns(2),
            ]);
    }
}
