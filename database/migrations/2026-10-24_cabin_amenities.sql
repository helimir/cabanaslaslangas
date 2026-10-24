-- ============================================================
-- Migración: equipamiento configurable por cabaña
-- Fecha: 2026-10-24
--
-- Crea la tabla de relación cabaña <-> equipamiento y asigna a las
-- cabañas existentes todo el equipamiento activo (para mantener el
-- comportamiento anterior: todas las cabañas mostraban todo).
--
-- Es idempotente: se puede ejecutar más de una vez.
-- ============================================================

CREATE TABLE IF NOT EXISTS `cabin_amenities` (
  `cabin_id` int(11) NOT NULL,
  `amenity_id` int(11) NOT NULL,
  PRIMARY KEY (`cabin_id`,`amenity_id`),
  KEY `amenity_id` (`amenity_id`),
  CONSTRAINT `cabin_amenities_cabin_fk` FOREIGN KEY (`cabin_id`) REFERENCES `cabins` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cabin_amenities_amenity_fk` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Asignar el equipamiento activo a las cabañas que todavía no tengan ninguna asignación.
INSERT IGNORE INTO `cabin_amenities` (`cabin_id`, `amenity_id`)
SELECT c.id, a.id
FROM `cabins` c
CROSS JOIN `amenities` a
WHERE a.status = 1
  AND NOT EXISTS (
      SELECT 1 FROM `cabin_amenities` ca WHERE ca.cabin_id = c.id
  );
