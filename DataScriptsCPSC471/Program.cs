using Microsoft.VisualBasic.FileIO;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace DataScriptsCPSC471
{
    public class Program
    {

        static Random _random = new Random();

        static string SQLConnectionString { get { return string.Format("Server={0};Database={1};User Id={2};Password={3};", "136.159.7.84 ", "CPSC471_Fall2016_G7", "CPSC471_Fall2016_G7", "a\"-na9o$^`I&\"nw"); } }

        public static void Main(string[] args)
        {
            //createNetworkOfFlights();
            setPaths();
            //insertAirportTableInfo("../../AllAirport.csv");
            //insertAirlineTableInfo("../../AllCanadianAirlines.csv");
        }

        /// <summary>
        /// Generate Flight Paths and submit them to the database using VS SQL Connection
        /// </summary>
        public static void setPaths()
        {
           //Get Flights
            using (DatabaseClassDataContext database = new DatabaseClassDataContext())
            {
                //Initial Association of Paths with Flights
                List<FLIGHT> flightsFetched = database.FLIGHTs.ToList();
                List<PATH> pathsCreated = new List<PATH>();
                foreach(FLIGHT f in flightsFetched)
                {
                    PATH path = new PATH()
                    {
                        airportname_2 = f.arrival_airport,
                        aiportname_1 = f.departure_airport,
                        distance = f.distance,
                        flight_id = f.Flight_id,
                    };
                    pathsCreated.Add(path);
                }

                database.PATHs.InsertAllOnSubmit(pathsCreated);
                database.SubmitChanges();

                //Generate a few flights with more than one path. 
                List<AIRPORT> canadianAirports = database.AIRPORTs.Where(x => x.MAJOR_CITY.COUNTRY.Name == "Canada").ToList();
                List<AIRPORT> usAirports = database.AIRPORTs.Where(x => x.MAJOR_CITY.COUNTRY.Name == "US").ToList();
                List<AIRPORT> mexicoAirports = database.AIRPORTs.Where(x => x.MAJOR_CITY.COUNTRY.Name == "Mexico").ToList();

                //Alternate frequency between countries for variation
                int freqCanUS = 5;
                int freqCanMex = 2;
                int freqMexUS = 3;

                //Factor for Minimum Range Distance of Flight
                int minimumDistFactor = 3;

                //US -Canada
                for(int i = 0;i< freqCanUS; i++)
                {
                    FLIGHT flight1 = generateFlight(database, canadianAirports[_random.Next(0, canadianAirports.Count)], usAirports[_random.Next(0, usAirports.Count)], _random.Next(2000, 5000));
                    database.FLIGHTs.InsertOnSubmit(flight1);
                    PATH p1 = new PATH()
                    {
                        airportname_2 = usAirports[_random.Next(0, usAirports.Count)].Name,
                        aiportname_1 = flight1.departure_airport,
                        distance =flight1.distance - _random.Next(100, flight1.distance/ minimumDistFactor),
                        flight_id = flight1.Flight_id,
                    };

                    PATH p2 = new PATH()
                    {
                        airportname_2 = flight1.arrival_airport,
                        aiportname_1 = p1.airportname_2,
                        distance = flight1.distance - p1.distance,
                        flight_id = flight1.Flight_id,
                    };

                    database.PATHs.InsertOnSubmit(p1);
                    database.PATHs.InsertOnSubmit(p2);
                }

                //Mexico and Canada
                for (int i = 0; i < freqCanMex; i++)
                {
                    FLIGHT flight1 = generateFlight(database, canadianAirports[_random.Next(0, canadianAirports.Count)], mexicoAirports[_random.Next(0, mexicoAirports.Count)], _random.Next(4000, 6000));
                    database.FLIGHTs.InsertOnSubmit(flight1);
                    PATH p1 = new PATH()
                    {
                        airportname_2 = canadianAirports[_random.Next(0, canadianAirports.Count)].Name,
                        aiportname_1 = flight1.departure_airport,
                        distance = flight1.distance - _random.Next(100, flight1.distance / minimumDistFactor),
                        flight_id = flight1.Flight_id,
                    };

                    PATH p2 = new PATH()
                    {
                        airportname_2 = flight1.arrival_airport,
                        aiportname_1 = p1.airportname_2,
                        distance = flight1.distance - p1.distance,
                        flight_id = flight1.Flight_id,
                    };

                    database.PATHs.InsertOnSubmit(p1);
                    database.PATHs.InsertOnSubmit(p2);
                }
                

                //Mexico-US
                for (int i = 0; i < freqMexUS; i++)
                {
                    FLIGHT flight1 = generateFlight(database, usAirports[_random.Next(0, usAirports.Count)], usAirports[_random.Next(0, usAirports.Count)], _random.Next(3000, 4000));
                    database.FLIGHTs.InsertOnSubmit(flight1);
                    PATH p1 = new PATH()
                    {
                        airportname_2 = usAirports[_random.Next(0, usAirports.Count)].Name,
                        aiportname_1 = flight1.departure_airport,
                        distance = flight1.distance - _random.Next(100, flight1.distance / minimumDistFactor),
                        flight_id = flight1.Flight_id,
                    };

                    PATH p2 = new PATH()
                    {
                        airportname_2 = flight1.arrival_airport,
                        aiportname_1 = p1.airportname_2,
                        distance = flight1.distance - p1.distance,
                        flight_id = flight1.Flight_id,
                    };

                    database.PATHs.InsertOnSubmit(p1);
                    database.PATHs.InsertOnSubmit(p2);
                }
                database.SubmitChanges();

            }
        }

        /// <summary>
        /// Generate Additional Flights
        /// </summary>
        /// <param name="database"></param>
        /// <param name="a1">Airport Start Point</param>
        /// <param name="a2">Airport End Point</param>
        /// <param name="distance">Distance of Flight</param>
        /// <returns></returns>
        public static FLIGHT generateFlight(DatabaseClassDataContext database, AIRPORT a1, AIRPORT a2, int distance)
        {
            string flightid = GenerateFlightNumber();
            while (database.FLIGHTs.SingleOrDefault(x => x.Flight_id.Equals(flightid)) != null)
            {
                flightid = GenerateFlightNumber();
                Console.WriteLine("Generating New Unique Flight Number");
            }
            DateTime deptTime = DateTime.Now.AddMinutes(30*_random.Next(0,4));

            DataScriptsCPSC471.FLIGHT flight = new DataScriptsCPSC471.FLIGHT()
            {
                Flight_id = flightid,
                arrival_airport = a1.Name,
                departure_airport = a2.Name,
                departure_time = deptTime ,
                arrival_time = deptTime.AddMinutes((((double)distance / (double)926) * _random.Next(600,1000))*3),
                distance = distance,
                base_price = 600
            };
            return flight;
        }

        /// <summary>
        /// Insert Airport into Database given read city and airport from file
        /// </summary>
        /// <param name="cityName">City containing Airport</param>
        /// <param name="airportName">Airport Name</param>
        /// <returns></returns>
        public async static Task InsertAirportInDatabase(string cityName, string airportName)
        {
            await Task.Factory.StartNew(() =>
            {
                using (DatabaseClassDataContext database = new DatabaseClassDataContext())
                {
                    DataScriptsCPSC471.AIRPORT Airport = new DataScriptsCPSC471.AIRPORT()
                    {
                        CityName = cityName,
                        Name = airportName
                    };
                    DataScriptsCPSC471.AIRPORT check = database.AIRPORTs.SingleOrDefault(x => x.Name.Equals(airportName));
                    if (check == null)
                    {
                        database.AIRPORTs.InsertOnSubmit(Airport);
                        database.SubmitChanges();
                    }
                }
            });
        }

        /// <summary>
        /// Insert Major Cities of Countries
        /// </summary>
        /// <param name="cityName">Name of City</param>
        /// <param name="countryName">Respective country containing given city</param>
        /// <returns></returns>
        public async static Task InsertMajorCityInDatabase(string cityName, string countryName)
        {
            await Task.Factory.StartNew(() =>
            {
                using (DatabaseClassDataContext database = new DatabaseClassDataContext())
                {
                    DataScriptsCPSC471.MAJOR_CITY city = new DataScriptsCPSC471.MAJOR_CITY()
                    {
                        Name = cityName,
                        CountryName = countryName
                    };
                    DataScriptsCPSC471.MAJOR_CITY check = database.MAJOR_CITies.SingleOrDefault(x => x.Name.Equals(cityName));
                    if (check == null)
                    {
                        database.MAJOR_CITies.InsertOnSubmit(city);
                        database.SubmitChanges();
                    }
                }
            });
        }

        /// <summary>
        /// Insert Airline associated with that city
        /// </summary>
        /// <param name="cityName">City associated with given Airline</param>
        /// <param name="airline">Name of Airline</param>
        /// <returns></returns>
        public async static Task InsertAirlineInDatabase(string cityName, string airline)
        {
            await Task.Factory.StartNew(() =>
            {
                using (DatabaseClassDataContext database = new DatabaseClassDataContext())
                {
                    DataScriptsCPSC471.COMPANY companyToAdd = new DataScriptsCPSC471.COMPANY()
                    {
                        CityName = cityName,
                        Name = airline
                    };
                    DataScriptsCPSC471.COMPANY check = database.COMPANies.SingleOrDefault(x => x.Name.Equals(airline));
                    if (check == null)
                    {

                        database.COMPANies.InsertOnSubmit(companyToAdd);
                        database.SubmitChanges();
                    }
                }
            });
        }

        /// <summary>
        /// Read File and Populate Airport Table in Database
        /// </summary>
        /// <param name="filename">Name of File containing airport information with Major Cities, Airports and Countries</param>
        public static void InsertAirportTableInfo(string filename)
        {
            //Read File 
            using (TextFieldParser parser = new TextFieldParser(@"" + filename + ""))
            {
                parser.TextFieldType = FieldType.Delimited;
                parser.SetDelimiters(",");
                while (!parser.EndOfData)
                {
                    //Process row
                    string[] fields = parser.ReadFields();
                    if (fields[0] != "Country")
                    {
                        //Insert Row
                        string country = fields[0].Trim();
                        string city = fields[1].Trim();
                        string airportName = fields[2].Trim();
                        InsertAirportInDatabase(city, airportName).Wait();
                        InsertMajorCityInDatabase(city, country).Wait();
                    }

                }
            }

        }

        /// <summary>
        /// Read File and Populate Airport Table in Database
        /// </summary>
        /// <param name="filename">Name of File containing airline information with cities, airlines and airports</param>
        public static void InsertAirlineTableInfo(string filename)
        {
            //Read File 
            using (TextFieldParser parser = new TextFieldParser(@"" + filename + ""))
            {
                parser.TextFieldType = FieldType.Delimited;
                parser.SetDelimiters(",");
                while (!parser.EndOfData)
                {
                    //Process row
                    string[] fields = parser.ReadFields();

                    //Insert Row
                    string city = fields[2].Trim();
                    string airportName = fields[1].Trim();
                    InsertAirlineInDatabase(city, airportName).Wait();

                }
            }

        }

        /// <summary>
        /// Generate Valid Flight Number
        /// </summary>
        /// <returns></returns>
        public static string GenerateFlightNumber()
        {
            string code = GetAirCodeLetter("x") + GetAirCodeLetter("x") + GetAirCodeLetter("ao") + GetAirCodeLetter("n") + GetAirCodeLetter("no") + GetAirCodeLetter("no") + GetAirCodeLetter("no") + GetAirCodeLetter("ao");
            code = code.Trim();
            while (code.Contains(" "))
            {
                code = code.Replace(" ", "");
                Console.WriteLine(code);
            }
            return code;
        }

        /// <summary>
        /// Generate Valid Aircode Letter
        /// </summary>
        /// <param name="specify">Specity type of code for return</param>
        /// <returns>Random string Code as specified</returns>
        public static string GetAirCodeLetter(string specify)
        {
            int someCase;
            if (specify == "x")
            {
                someCase = _random.Next(0, 3);
            }
            else if (specify == "a")
            {
                someCase = _random.Next(0, 2);
            }
            else if (specify == "n")
            {
                someCase = 2;
            }
            else if (specify == "no")
            {
                int optional = _random.Next(0, 2);
                if (optional == 1)
                {
                    return ' '.ToString();
                }
                someCase = 2;

            }
            else if (specify == "ao")
            {
                int optional = _random.Next(0, 2);
                if (optional == 1)
                {
                    return ' '.ToString();
                }
                someCase = _random.Next(0, 2);
            }
            else
            {
                return " ";
            }

            switch (someCase)
            {
                case 0:
                    int num = _random.Next(0, 26); // Zero to 25
                    char let = (char)('a' + num);
                    return let.ToString();
                case 1:
                    int num1 = _random.Next(0, 26); // Zero to 25
                    char let1 = (char)('A' + num1);
                    return let1.ToString();
                case 2:
                    int num2 = _random.Next(0, 10); // Zero to 9
                    return num2.ToString();
            }
            return " ";
        }

        /// <summary>
        /// Create Network of flights between major cities with varied levels of distance
        /// </summary>
        public static void createNetworkOfFlights()
        {
            using (DatabaseClassDataContext database = new DatabaseClassDataContext())
            {
                List<MAJOR_CITY> fetchedCities = database.MAJOR_CITies.ToList();
                List<MAJOR_CITY> fetchedCities2 = database.MAJOR_CITies.ToList();
                fetchedCities2.Reverse();
                foreach (MAJOR_CITY c in fetchedCities)
                {
                    foreach (MAJOR_CITY c2 in fetchedCities2)
                    {
                        if (c2.Name != c.Name )
                        {
                            //Generate Flights on various levels of Distance
                            if (c2.CountryName == "Canada" && c.CountryName == "Canada")
                            {
                                int distance = _random.Next(250, 4501);
                                AddFlight(distance, c, c2, database);
                            }
                            else if (c2.CountryName == "US" && c.CountryName == "US")
                            {
                                int distance = _random.Next(250, 2093);
                                AddFlight(distance, c, c2, database);
                            }
                            else if (c2.CountryName == "Mexico" && c.CountryName == "Mexico")
                            {
                                int distance = 300;
                                AddFlight(distance, c, c2, database);

                            }
                            else if (c2.CountryName == "Canada" && c.CountryName == "Mexico")
                            {
                                int distance = 4500;
                                AddFlight(distance, c, c2, database);

                            }
                            else if (c2.CountryName == "Canada" && c.CountryName == "US")
                            {
                                int distance = _random.Next(500, 3501);
                                AddFlight(distance, c, c2, database);
                            }
                            else if (c2.CountryName == "US" && c.CountryName == "Canada")
                            {
                                int distance = _random.Next(500, 3501);
                                AddFlight(distance, c, c2, database);
                            }
                            else if (c2.CountryName == "US" && c.CountryName == "Mexico")
                            {
                                int distance = _random.Next(400, 3501);
                                AddFlight(distance, c, c2, database);

                            }
                            else if (c2.CountryName == "Mexico" && c.CountryName == "US")
                            {
                                int distance = _random.Next(400, 3501);
                                AddFlight(distance, c, c2, database);
                            }
                        }
                    }
                }

            }
        }

        /// <summary>
        /// Abandoned Code for additional feature to generate Sellers
        /// </summary>
        public static void generateSellersOfFlights()
        {
            //Airlines will be randomly assigned (but chosen perhaps based on cities. If no such one. Air canada it is.) 
        }

        /// <summary>
        /// Add generated flight info into database
        /// </summary>
        /// <param name="distance">Distance of Flight generateed</param>
        /// <param name="c">Major City Start</param>
        /// <param name="c2">Major City End</param>
        /// <param name="database">Database Connection Reference</param>
        public static void AddFlight(int distance, MAJOR_CITY c, MAJOR_CITY c2, DatabaseClassDataContext database)
        {
            int fetch = 0;
            int fetch2 = 0;
            if (database.AIRPORTs.Where(x => x.CityName.Equals(c.Name)).ToList().Count > 1)
                fetch = _random.Next(0, 2);

            if (database.AIRPORTs.Where(x => x.CityName.Equals(c2.Name)).ToList().Count > 1)
                fetch2 = _random.Next(0, 2);
            DataScriptsCPSC471.AIRPORT departureAirport = database.AIRPORTs.Where(x => x.CityName.Equals(c.Name)).ToList()[fetch];


            DataScriptsCPSC471.AIRPORT arrivalAirport = database.AIRPORTs.Where(x => x.CityName.Equals(c2.Name)).ToList()[fetch2];

            string deptAirportName = departureAirport.Name;
            string arrivalAirportName = arrivalAirport.Name;
            
            //Debugging Boolean Check to ensure the flight does not already exist
            //if (database.FLIGHTs.Where(x => x.departure_airport.Equals(deptAirportName) && x.arrival_airport.Equals(arrivalAirportName)).ToList().Count> 0 )
            //{
            //    FLIGHT f = database.FLIGHTs.Where(x => x.departure_airport.Equals(deptAirportName) && x.arrival_airport.Equals(arrivalAirportName)).ToList()[0];
            //    Console.WriteLine("Flight Exists!");
            //    Console.WriteLine(f.arrival_time);
            //    Console.WriteLine(f.departure_time); 
            //    return;
            //}



            string flightid = GenerateFlightNumber();
            while (database.FLIGHTs.SingleOrDefault(x => x.Flight_id.Equals(flightid)) != null)
            {
                flightid = GenerateFlightNumber();
                Console.WriteLine("Generating New Unique Flight Number");
            }
            DateTime deptTime = DateTime.Now.AddMinutes(30*_random.Next(0,4));

            DataScriptsCPSC471.FLIGHT flight = new DataScriptsCPSC471.FLIGHT()
            {
                Flight_id = flightid,
                arrival_airport = arrivalAirportName,
                departure_airport = deptAirportName,
                departure_time = deptTime ,
                arrival_time = deptTime.AddMinutes(((double)distance / (double)926) * _random.Next(600,1000)),
                distance = distance,
                base_price = 600
            };
            database.FLIGHTs.InsertOnSubmit(flight);
            database.SubmitChanges();
        }

    }
}
