## Project Vision

> Reference: *BDD in Action* Section 3.3 p. 67

1. I wish to learn more about "machine learning" for professional development purposes, and
   share that knowledge with other PHP developers. I intend to next explore "deep learning".

2. Since this is a side project (potentially Open Source), I need to ensure no proprietary
   data or code is used.
   
3. I have chosen to use track data from my Garmin hand-held GPS unit as a way of ensuring
   that my "big data" set is free of any employment entanglements.
   
4. My GPS data can only be shared if it sanitized, i.e., free of privacy concerns.

## Privatize Waypoints

> Reference: *Specification by Example* "Deriving scope from goals" p. 19

Definitions:

  * Waypoint: A specific location (latitude, longitude, elevation) identified as a point of
    interest. Example: Perkins Restaurant in Hastings, Minnesota.
  * Track point: The series of locations with timestamp showing where the GPS unit was at a
    specific point in time. The path of travel can then be plotted on a map.
    
The feature:

1. One step in sanitizing my GPS data is to remove (privatize) all records that are within
   five miles of a private residence. For example, the waypoint identified as "My House" should
   be privatized, and all track points which are within five miles of that waypoint should also
   be privatized.

2. This feature provides the ability to mark waypoints as private.

3. A later feature will mark track points as private based on being within five miles of any
   private waypoint.
