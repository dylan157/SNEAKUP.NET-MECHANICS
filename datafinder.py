# Author:      Dylan Spriddle
# Description: Import csv data into PostGres.
# Date:        December 2016.

import psycopg2
import sys
import argparse
import datetime
#import hashlib.md5
from   subprocess import call
import time
import filecmp
with open("config.txt", "rt") as file: #contains postgres info
    constr = file.read()

parser = argparse.ArgumentParser(description='Load ONS data into PostGres.')
parser.add_argument("-d", help="Don't actually download the file.")
args = parser.parse_args()

#Variables###########################
str_mytime = str(datetime.datetime.now().strftime("%Y%m%d_%H:%M:%S"))
debug = True
i = 0
mystr = ''
par0 = '';  par1 = '';  par2 = '';  par3 = '';  par4 = ''
par5 = '';  par6 = '';  par7 = '';  par8 = '';  par9 = ''
par10 = ''; par11 = ''; par12 = ''; par13 = ''; par14 = ''; par15 = ''
####################################

try:
    conn=psycopg2.connect(constr)
    print("Connected to the database.")
except:
    print("Cannot connect to the database.")
    sys.exit()

try:
    call(["wget", "http://prod.publicdata.landregistry.gov.uk.s3-website-eu-west-1.amazonaws.com/pp-monthly-update-new-version.csv"])
    print("File has been downloaded.")
    if filecmp.cmp('pp-monthly-update-new-version.csv', 'old.csv'): #downloads the file and checks if its not already in the database.
        call(['rm', 'pp-monthly-update-new-version.csv'])
        with open( 'updatelog.txt' ,'a' ) as logf:
            logf.write(str_mytime+' F '+'\n') # if already in database: log the date and 'F' for FAIL
        sys.exit()
except:
    print("Could not get file from website.")
    sys.exit()



with open('./log_' + str_mytime + '.txt', 'w') as output:
    output.writelines("###############################################") # logs 0.0
    output.writelines("")
    output.writelines("Logging " + str_mytime)

cur = conn.cursor()
try:
    with open('pp-monthly-update-new-version.csv', 'r') as file: #open the file
        print("Importing data...")
        for line in file:

            i += 1

            mylist = line.split('","')

            if (i % 100 == 0): # use modular division to efficently commit to database
                conn.commit()

                if (debug):
                    print(str(i) + " rows inserted...")              

      
            par0 = mylist[0].replace('"',"'")
            par1 = mylist[1].replace("'","^")
            par2 = mylist[2].replace("'","^")
            par3 = mylist[3].replace("'","^")
            par4 = mylist[4].replace("'","^")
            par5 = mylist[5].replace("'","^")
            par6 = mylist[6].replace("'","^")
            par7 = mylist[7].replace("'","^")
            par8 = mylist[8].replace("'","^") 
            par9 = mylist[9].replace("'","^")
            par10 = mylist[10].replace("'","^")
            par11 = mylist[11].replace("'","^")
            par12 = mylist[12].replace("'","^")
            par13 = mylist[13].replace("'","^")
            par14 = mylist[14].replace("'","^")
            par15 = mylist[15].replace('"',"'")
     

            mystr = "select add_new_property(" + par0 + "', '"  + par1 + "', '"  + par2 + "', '"  + \
                    par3 + "', '" +          par4 + "', '"  + par5 + "', '"  + par6 + "', '"  + \
                    par7 + "', '" +          par8 + "', '"  + par9 + "', '"  + par10 + "', '" + \
                    par11 + "', '" +         par12 + "', '" + par13 + "', '" + par14 + "', '" + par15 + ");" # Query string
            try:
				cur.execute(mystr) #execute query	
				rows = cur.fetchall() #read output:
				for row in rows:
					if (row[0] == 1):
						print ("We got a duplicate....")
						print(mystr)
						print("Duplicate on line: " + str(i))
						with open('./log_' + str_mytime + '.txt', 'w') as output:
							output.writelines("Duplicate on line: " + str(i) + mystr)
            except:
                e = sys.exc_info()[0]
                print ("Error, failed on line " + str(i) + " Error = ", e)
                print(mystr)
                sys.exit()                 
except:
    e = sys.exc_info()[0]
    print ("Error SQL:", e)
    sys.exit()
    
mystr = "Loaded " + str(i) + " rows." #print total rows inserted
print(mystr)
with open('./log_' + str_mytime + '.txt', 'w') as output:
    output.writelines(mystr)

mystr = "Renaming file."
print(mystr)
with open( 'updatelog.txt' ,'a' ) as logf:
    logf.write(str_mytime+' UPDATE! '+'\n')

call(["rm", "old.csv"])

call(["mv", "pp-monthly-update-new-version.csv", "old.csv"])    

    
print("Commiting transaction.")
cur.close()
conn.commit()
conn.close()
print("")
print('Finished.')
#/end
