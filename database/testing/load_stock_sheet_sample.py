import csv
import json
import re
import sqlite3
from datetime import datetime
from pathlib import Path


ROOT = Path(__file__).resolve().parents[2]
DB_PATH = ROOT / "database" / "database.sqlite"
CSV_PATH = ROOT / "database" / "testing" / "stock_sheet_sample.csv"


def slugify(value: str) -> str:
    value = value.lower().strip()
    value = re.sub(r"[^a-z0-9]+", "-", value)
    value = re.sub(r"-{2,}", "-", value).strip("-")
    return value or "category"


def ensure_total_ht_column(cursor: sqlite3.Cursor) -> None:
    cursor.execute("PRAGMA table_info(products)")
    columns = [row[1] for row in cursor.fetchall()]

    if "total_ht" not in columns:
        cursor.execute("ALTER TABLE products ADD COLUMN total_ht NUMERIC DEFAULT '0'")


def get_or_create_unit(cursor: sqlite3.Cursor) -> int:
    cursor.execute("SELECT id FROM units WHERE symbol = ?", ("pc",))
    row = cursor.fetchone()
    if row:
        return row[0]

    now = datetime.utcnow().isoformat(sep=" ", timespec="seconds")
    cursor.execute(
        "INSERT INTO units (name, symbol, created_at, updated_at) VALUES (?, ?, ?, ?)",
        ("Piece", "pc", now, now),
    )
    return cursor.lastrowid


def get_or_create_category(cursor: sqlite3.Cursor, code: str, name: str) -> int:
    cursor.execute("SELECT id, slug FROM categories WHERE name = ?", (name,))
    row = cursor.fetchone()
    if row:
        cursor.execute(
            "UPDATE categories SET code = ?, updated_at = ? WHERE id = ?",
            (code, datetime.utcnow().isoformat(sep=' ', timespec='seconds'), row[0]),
        )
        return row[0]

    base_slug = slugify(name)
    slug = base_slug
    counter = 1
    while True:
        cursor.execute("SELECT 1 FROM categories WHERE slug = ?", (slug,))
        if not cursor.fetchone():
            break
        counter += 1
        slug = f"{base_slug}-{counter}"

    now = datetime.utcnow().isoformat(sep=" ", timespec="seconds")
    cursor.execute(
        """
        INSERT INTO categories (code, name, slug, description, created_at, updated_at, custom_fields)
        VALUES (?, ?, ?, ?, ?, ?, ?)
        """,
        (code, name, slug, None, now, now, None),
    )
    return cursor.lastrowid


def get_or_create_supplier(cursor: sqlite3.Cursor, name: str | None) -> int | None:
    if not name:
        return None

    cursor.execute("SELECT id FROM suppliers WHERE name = ?", (name,))
    row = cursor.fetchone()
    if row:
        return row[0]

    now = datetime.utcnow().isoformat(sep=" ", timespec="seconds")
    cursor.execute(
        """
        INSERT INTO suppliers (name, contact_person, email, phone, address, notes, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        """,
        (name, "", None, None, None, "Imported from stock sheet sample", now, now),
    )
    return cursor.lastrowid


def upsert_product(cursor: sqlite3.Cursor, unit_id: int, row: dict[str, str]) -> None:
    category_id = get_or_create_category(cursor, row["Famille Article"], row["Designation Famille"])
    supplier_id = get_or_create_supplier(cursor, row["Fournisseur"] or None)
    now = datetime.utcnow().isoformat(sep=" ", timespec="seconds")
    total_ht = float(row["Total HT"] or 0)
    purchase_price_eur = float(row["Prix d'achat EUR"] or 0)
    unit_pr_ht = float(row["PR unitaire HT"] or 0)
    quantity = int(float(row["Qte"] or 0))

    cursor.execute("SELECT id FROM products WHERE reference = ?", (row["Reference Article"],))
    existing = cursor.fetchone()

    payload = (
        category_id,
        supplier_id,
        unit_id,
        row["Reference Article"],
        row["REF FR"] or None,
        row["Designation Article"],
        row["Designation 2"] or None,
        purchase_price_eur,
        unit_pr_ht,
        0,
        quantity,
        0,
        1,
        row["Designation Article"],
        json.dumps({"source": "stock_sheet_sample"}),
        total_ht,
        now,
    )

    if existing:
        cursor.execute(
            """
            UPDATE products
            SET category_id = ?, supplier_id = ?, unit_id = ?, reference = ?, ref_fr = ?, designation = ?,
                designation_2 = ?, purchase_price_eur = ?, unit_pr_ht = ?, selling_price = ?, quantity = ?,
                min_stock = ?, is_active = ?, description = ?, notes = ?, total_ht = ?, updated_at = ?
            WHERE id = ?
            """,
            payload + (existing[0],),
        )
        return

    cursor.execute(
        """
        INSERT INTO products (
            category_id, supplier_id, unit_id, reference, ref_fr, designation, designation_2,
            purchase_price_eur, unit_pr_ht, selling_price, quantity, min_stock, is_active,
            description, notes, total_ht, created_at, updated_at, custom_fields
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        """,
        payload + (now, None),
    )


def main() -> None:
    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()

    ensure_total_ht_column(cursor)
    unit_id = get_or_create_unit(cursor)

    with CSV_PATH.open("r", encoding="utf-8-sig", newline="") as handle:
        reader = csv.DictReader(handle)
        for row in reader:
            upsert_product(cursor, unit_id, row)

    conn.commit()

    cursor.execute("SELECT COUNT(*) FROM products")
    product_count = cursor.fetchone()[0]
    cursor.execute("SELECT COUNT(*) FROM categories")
    category_count = cursor.fetchone()[0]
    cursor.execute("SELECT COUNT(*) FROM suppliers")
    supplier_count = cursor.fetchone()[0]

    print(f"Imported sample stock data into {DB_PATH}")
    print(f"Products: {product_count}")
    print(f"Categories: {category_count}")
    print(f"Suppliers: {supplier_count}")


if __name__ == "__main__":
    main()
