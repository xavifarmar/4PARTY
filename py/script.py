import csv
import pymysql as MySQLdb

# Hacer conexión
db = MySQLdb.connect(host="localhost",
                     user="root",
                     passwd="",
                     db="4party")

cursor = db.cursor()

def read_csv():
    # Leer archivo CSV
    with open('./productos.csv', mode="r") as file:
        reader = csv.reader(file, delimiter=";")
        for row in reader:
            # Extraer cada valor del CSV
            product_name = row[0]
            categories = row[1]
            types = row[2]
            colors = row[3]
            url_image = row[4]
            genders = 0
            price = 10  # Asignar un precio válido

            color_id_value = color_id(colors)
            category_id_value = category_id(categories)
            gender_id_value = gender_id(genders)
            type_id_value = type_id(types)
            
            # Insertar en la tabla productos
            query_product = (
                "INSERT INTO products (name, price, stock, category_id, color_id, gender_id, clothing_type_id, created_at) "
                "VALUES (%s, %s, 100, %s, %s, 1, %s, NOW())"
            )
            cursor.execute(query_product, (product_name, price, category_id_value, color_id_value, type_id_value))
            db.commit()  # Asegúrate de hacer commit para que se guarde en la base de datos

            # Insertar en la tabla product_images
            query_images = "INSERT INTO product_images (product_id, image_url, is_primary) VALUES (%s, %s, %s)"
            cursor.execute(query_images, (cursor.lastrowid, url_image, 1))
            db.commit()

def color_id(colors):
    colors_id = {
        'Rojo': 1,
        'Azul marino': 2,
        'Negro': 3,
        'Blanco': 4,
        'Dorado': 5,
        'Plateado': 6,
        'Verde': 7,
        'Rosa': 8,
        'Morado': 9,
        'Gris': 10,
        'Amarillo': 11,
        'Naranja': 12,
        'Beige': 13,
        'Vino': 14,
        'Turquesa': 15,
        'Marron': 16
    }
    return colors_id.get(colors, None)

def category_id(categories):
    categories_id = {
        'Vestidos': 1,
        'Trajes': 2, 
        'Camisas': 3,
        'Pantalones': 4, 
        'Accesorios': 5, 
        'Zapatos': 6,
        'Joyeria': 7,
        'Chaquetas': 8,
        'Cinturones': 9,
        'Corbatas': 10,
        'Pajaritas': 11,
        'Anillos': 12,
    }
    return categories_id.get(categories, None)

def type_id(types):
    types_id = {
        "Vestido largo": 1,
        "Vestido corto": 2,
        "Traje": 3,
        "Traje de noche": 4,
        "Falda": 5,
        "Blusa": 6,
        "Mono": 7,
        "Top": 8,
        "Chaqueta": 9,
        "Blazer": 10,
        "Pantalón": 11,
        "Abrigo": 12,
        "Camiseta": 13,
        "Chaleco": 14,
        "Pantalón de vestir": 15,
        "Chaqueta de noche": 16,
        "Saco": 17,
        "Falda de cóctel": 18,
        "Top elegante": 19,
        "Mono largo": 20,
        "Jersey": 21,
        "Camisa larga": 22,
        "Camisa corta": 23,
        "Zapatos de traje": 24
    }
    return types_id.get(types, None)

def gender_id(genders):
    genders_id = {
        'Masculino': 1,
        'Femenino': 2,
        'Unisex': 3  
    }
    return genders_id.get(genders, None)

# Llamar a la función para procesar el CSV
read_csv()

# Cerrar la conexión a la base de datos
cursor.close()
db.close()



# HACER LAS DOS CONSULTAS DE SQL COGIENDO LO NECESARIO DE CADA UNA Y QUE SE HAGAN SIMULTANEAMENTE

#CONSULTA PARA INSERTAR EN PRODUCTS





#"INSERT INTO `products`(`name`, `description`, `price`, `stock`, `category_id`, `color_id`, `gender_id`, "
#"`clothing_type_id`, `view_count`, `like_count`, `created_at`) VALUES ('Zapato soft piel',NULL , 10 ,100, 1, 2, 1, 1, 0, 0, NOW());"



    


