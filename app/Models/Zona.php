<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Zona extends Model
{
    use HasFactory;

    protected $table = 'zonas';

    protected $fillable = [
        'nombre',
        'area',
    ];

    protected $appends = [
        'area',
    ];

    public function setAreaAttribute($value): void
    {
        if (is_array($value)) {
            $this->attributes['area'] = DB::raw("ST_GeomFromText('" . self::buildPolygonWkt($value) . "', 4326)");
        }
    }

    /**
     * Parsear geometry Polygon a array de puntos [{lat, lng}, ...]
     */
    public function getAreaAttribute(): ?array
    {
        if (!$this->id) {
            return null;
        }

        $result = DB::selectOne('SELECT ST_AsGeoJSON(area) as area_geojson FROM zonas WHERE id = ?', [$this->id]);

        if (!$result || !$result->area_geojson) {
            return null;
        }

        $geojson = json_decode($result->area_geojson, true);
        $coordinates = $geojson['coordinates'][0] ?? [];

        if (count($coordinates) > 1 && $coordinates[0] === $coordinates[count($coordinates) - 1]) {
            array_pop($coordinates);
        }

        return array_map(fn($point) => ['lat' => (float) $point[1], 'lng' => (float) $point[0]], $coordinates);
    }

    /**
     * Usuarios asignados a esta zona
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'id_zona');
    }

    /**
     * Edificios en esta zona
     */
    public function edificios(): HasMany
    {
        return $this->hasMany(Edificio::class, 'id_zona');
    }

    public static function buildPolygonWkt(array $points): string
    {
        if (count($points) < 4) {
            throw new \InvalidArgumentException('El polígono debe contener al menos 4 puntos.');
        }

        $first = $points[0];
        $last = $points[count($points) - 1];

        if ($first !== $last) {
            $points[] = $first;
        }

        $coordinates = array_map(
            fn($point) => sprintf('%s %s', (float) $point['lng'], (float) $point['lat']),
            $points
        );

        return 'POLYGON((' . implode(', ', $coordinates) . '))';
    }
}
