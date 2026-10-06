<?php

namespace App\Enums;

enum StatusPedido: string
{
    case Pendente = 'pendente';
    case Confirmado = 'confirmado';
    case EmPreparo = 'em_preparo';
    case SaiuParaEntrega = 'saiu_para_entrega';
    case Entregue = 'entregue';
    case Cancelado = 'cancelado';

    /**
     * Human readable label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pendente => 'Pendente',
            self::Confirmado => 'Confirmado',
            self::EmPreparo => 'Em preparo',
            self::SaiuParaEntrega => 'Saiu para entrega',
            self::Entregue => 'Entregue',
            self::Cancelado => 'Cancelado',
        };
    }

    /**
     * Tailwind classes used by the status badge.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Pendente => 'bg-zinc-100 text-zinc-700 ring-zinc-300',
            self::Confirmado => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            self::EmPreparo => 'bg-lime-50 text-lime-700 ring-lime-200',
            self::SaiuParaEntrega => 'bg-teal-50 text-teal-700 ring-teal-200',
            self::Entregue => 'bg-emerald-600 text-white ring-emerald-600',
            self::Cancelado => 'bg-zinc-800 text-zinc-100 ring-zinc-800',
        };
    }
}
