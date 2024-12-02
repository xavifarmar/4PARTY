import csv
import MySQLdb

#Hacer conexion
db = MySQLdb.connect(host="localhost",
                     user="root",
                     passwd="",
                     db="4party")

cursor = db.cursor()

def  read_csv():
#Leer archivo csv
    with open ('productos.csv', mode="r") as file: 
        reader = csv.reader(file, delimiter=";")
        for row in reader:
            #Extraer cada valor del CSV
            product_name = row[0]
            categories = row[1]
            types = row[2]
            colors = row[3]
            url_image = row[4]
            genders = 0
            price = 0

            color_id_value = color_id(colors)
            category_id_value = category_id(categories)
            gender_id_value = gender_id(genders)
            type_id_value = type_id(types)
            
            #Insertar en la tabla productos
            query_product = (f"INSERT INTO products (name, price, stock, category_id, color_id, gender_id, clothing_type_id, created_at ) 
             VALUES (%s, %s, 100, %s, %s, %s, %s, %s, %s, %s, %s, NOW()")

            cursor.execute(query_product, (product_name, price, category_id_value, color_id_value, gender_id_value, type_id_value)
            
            #Insertar en la tabla productos

            
            )

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
        
        #Busca en Diccionario el color que hay
        colors_id.get(colors, None)



def category_id(categories):
     print ("Hola")
    
def type_id(types):
     print ("Hola")

def gender_id(genders):
     print ("Hola")


# HACER LAS DOS CONSULTAS DE SQL COGIENDO LO NECESARIO DE CADA UNA Y QUE SE HAGAN SIMULTANEAMENTE

#CONSULTA PARA INSERTAR EN PRODUCTS
f"INSERT INTO products (name, price, stock, category_id, color_id, gender_id, clothing_type_id, created_at ) VALUES ({product}, "

#CONSULTA PARA AÑADIR IMAGENES
"INSERT INTO product_images (id, product_id, image_url, is_primary) VALUES"








    


