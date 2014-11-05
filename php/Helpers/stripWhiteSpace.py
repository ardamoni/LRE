#!/usr/bin/env python
__author__ = 'ekke'
import sys, getopt, os

###############################
# o == option
# a == argument passed to the o
###############################
# Cache an error with try..except 
# Note: options is the string of option letters that the script wants to recognize, with 
# options that require an argument followed by a colon (':') i.e. -i fileName
#
try:
    myopts, args = getopt.getopt(sys.argv[1:],"i:h")
except getopt.GetoptError as e:
    print (str(e))
    print("Usage: %s -i input " % sys.argv[0])
    sys.exit(2)

for o, a in myopts:
	if o=='-h':
#	    print("HELP -- Usage: %s -i input " % sys.argv[0])
		print('Usage: %s -i input	 ' % sys.argv[0]) 
		sys.exit()
	elif o in ("-i","--ifile"):
		inputfile=a
		if not os.path.exists(inputfile):
			print('Input file does not exist') 
			sys.exit()
		print ("File name: %s" % inputfile)
		head, tail = os.path.split(str(sys.argv[2]))
		print ("Path: %s" % head)
		print (os.path.dirname(str(sys.argv[2])))
		print ("File name: %s" % tail)
		fname, fsuffix = tail.split(".")
		print ("File name stripped: %s" % fname)
# 		f = open(str(sys.argv[2]), 'r')
		f = open(str(inputfile), 'r')
		fout = open(head+"/"+fname+"-NoGremlins.kml", "w")
		for line in f:
			line = line.rstrip()    # remove ALL whitespaces on the right side, including '\n'
			line = line.replace(chr(2), "")
			line = line.replace(chr(0), "")
			fout.write(line+"\n")
			# do something with line
		fout.close()    
		f.close()