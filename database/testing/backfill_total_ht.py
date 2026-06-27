import sqlite3
from pathlib import Path


DB_PATH = Path(__file__).resolve().parents[2] / "database" / "database.sqlite"


def main() -> None:
    conn = sqlite3.connect(DB_PATH)
    cur = conn.cursor()

    cur.execute(
        """
        UPDATE products
        SET total_ht = ROUND(quantity * unit_pr_ht, 3)
        WHERE COALESCE(total_ht, 0) = 0
          AND COALESCE(quantity, 0) > 0
          AND COALESCE(unit_pr_ht, 0) > 0
        """
    )

    updated_rows = cur.rowcount
    conn.commit()

    cur.execute(
        "SELECT COUNT(*) FROM products WHERE COALESCE(total_ht, 0) = 0 AND quantity > 0 AND unit_pr_ht > 0"
    )
    remaining = cur.fetchone()[0]

    print(f"Backfilled total_ht for {updated_rows} products.")
    print(f"Remaining rows with missing total_ht despite qty and unit_pr_ht: {remaining}")


if __name__ == "__main__":
    main()
