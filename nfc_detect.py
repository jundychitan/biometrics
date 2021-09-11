#! /usr/bin/env python

from __future__ import print_function
from smartcard.scard import *
import smartcard.util
import sys
import time

srTreeATR = \
    [0x3B, 0x77, 0x94, 0x00, 0x00, 0x82, 0x30, 0x00, 0x13, 0x6C, 0x9F, 0x22]
srTreeMask = \
    [0xFF, 0xFF, 0xFF, 0xFF, 0xFF, 0xFF, 0xFF, 0xFF, 0xFF, 0xFF, 0xFF, 0xFF]


def printstate(state):
	reader, eventstate, atr = state
	print(reader + " " + smartcard.util.toHexString(atr, smartcard.util.HEX))
	if eventstate & SCARD_STATE_ATRMATCH:
		print('\tCard found')
	if eventstate & SCARD_STATE_UNAWARE:
		print('\tState unware')
	if eventstate & SCARD_STATE_IGNORE:
		print('\tIgnore reader')
	if eventstate & SCARD_STATE_UNAVAILABLE:
		print('\tReader unavailable')
	if eventstate & SCARD_STATE_EMPTY:
		print('\tReader empty')
	if eventstate & SCARD_STATE_PRESENT:
		print('\tCard present in reader')
	if eventstate & SCARD_STATE_EXCLUSIVE:
		print('\tCard allocated for exclusive use by another application')
	if eventstate & SCARD_STATE_INUSE:
		print('\tCard in used by another application but can be shared')
	if eventstate & SCARD_STATE_MUTE:
		print('\tCard is mute')
	if eventstate & SCARD_STATE_CHANGED:
		print('\tState changed')
	if eventstate & SCARD_STATE_UNKNOWN:
		print('\tState unknowned')


try:
	hresult, hcontext = SCardEstablishContext(SCARD_SCOPE_USER)
	if hresult != SCARD_S_SUCCESS:
		raise error(
			'Failed to establish context: ' + \
			SCardGetErrorMessage(hresult))
	print('Context established!')

	try:
		hresult, readers = SCardListReaders(hcontext, [])
		if hresult != SCARD_S_SUCCESS:
			raise error(
				'Failed to list readers: ' + \
				SCardGetErrorMessage(hresult))
		print('PCSC Readers:', readers)

		readerstates = []
		for i in range(len(readers)):
			readerstates += [(readers[i], SCARD_STATE_UNAWARE)]

		print('----- Current reader and card states are: -------')
		readingLoop = 1
		while(readingLoop):
			hresult, newstates = SCardGetStatusChange(hcontext, 0, readerstates)
			for i in newstates:
				#print (newstates)
				#printstate(i)
				reader, eventstate, atr = i
				if eventstate & SCARD_STATE_EMPTY:
					print("Card Released..")
					readingLoop=0
					break
			print("Card Detected..")
			time.sleep(1)

	finally:
		hresult = SCardReleaseContext(hcontext)
		if hresult != SCARD_S_SUCCESS:
			raise error(
				'Failed to release context: ' + \
				SCardGetErrorMessage(hresult))
		print('Released context.')

	import sys
	if 'win32' == sys.platform:
		print('press Enter to continue')
		sys.stdin.read(1)

except error as e:
	print(e)