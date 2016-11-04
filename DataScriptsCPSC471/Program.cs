using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace DataScriptsCPSC471
{
    public class Program
    {
        static string SQLConnectionString { get { return string.Format("Server={0};Database={1};User Id={2};Password={3};", "136.159.7.84 ", "CPSC471_Fall2016_G7", "CPSC471_Fall2016_G7", "a\"-na9o$^`I&\"nw"); } }
        public async static void ManuallyAddLayerInDatabase(string cityName, string airportName)
        {
            await Task.Factory.StartNew(() =>
            {
                using (DatabaseClassDataContext database = new DatabaseClassDataContext())
                {
                    DataScriptsCPSC471.AIRPORT layerToAdd = new DataScriptsCPSC471.AIRPORT()
                    {
                        CityName = cityName,
                        Name= airportName
                    };
                    database.AIRPORTs.InsertOnSubmit(layerToAdd);
                    database.SubmitChanges();
                }
            });
        }

        static void Main(string[] args)
        {
        }
    }
}
