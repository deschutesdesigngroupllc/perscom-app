<?php

namespace Database\Seeders\Demo\Military;

use App\Models\Award;
use Illuminate\Database\Seeder;

class AwardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $awards = [
            [
                'name' => 'Army Distinguished Service Cross',
                'description' => 'The Army Distinguished Service Cross Medal (DSC) is a U.S. Army decoration given for extreme gallantry and risk of life in actual combat with an armed enemy force. Operations which merit the DSC need to be of such a high degree to be above those mandatory for all other U.S. combat decorations but not meeting the criteria for the Medal of Honor. The DSC is equivalent to the Navy Cross (Navy and Marine Corps) and the Air Force Cross (Air Force). The DSC was first established and awarded during World War I. In accession, a number of awards were delegated for actions preceding World War I. In many cases, these were to soldiers who had acquired a Certificate of merit for gallantry which, at the time, was the only other honor beyond the Medal of Honor the Army could give. Others were delayed acknowledgement of actions in the Philippines, on the Mexican Border and during the Boxer Rebellion. This decoration should not be mistaken for the Distinguished Service Medal, which distinguishes meritorious service to the government of the U.S. (as a senior military officer or government official) rather than individual acts of bravery (as a member of the U.S. Army).',
            ],
            [
                'name' => 'Department of Defense Distinguished Service',
                'description' => "The Defense Distinguished Service Medal (DDSM) is presented to any member of the U.S. Armed Forces, while serving with the Department of Defense, who distinguishes themselves with exceptional performance of duty contributing to national security or defense of the United States. Created on July 9th, 1970 by President Richard Nixon's Executive Order 11545, the medals is typically awarded to senior officers such as the Chairman and Vice Chairman of the Joint Chiefs of Staff, the Chief and Vice Chiefs of the military services and other personnel whose duties bring them in direct and frequent contact with the Secretary of Defense, Deputy Secretary of Defense or other senior government officials.",
            ],
            [
                'name' => 'Army Distinguished Service',
                'description' => 'The Army Distinguished Service Medal (DSM) is granted to any soldier who, while serving in the U.S. Army, distinguishes themselves with exceptionally meritorious service to the U.S. in a duty of great responsibility. The achievement must be of a level as to merit acknowledgement for service that is positively "exceptional." Exceptional performance of ordinary duties does not alone justify the award. For service not associated with actual war, the term "duty of a great responsibility" applies to a restricted range of positions than in a time of war, and commands proof of conspicuously indicative achievement.',
            ],
            [
                'name' => 'Silver Star',
                'description' => '',
            ],
            [
                'name' => 'Defense Superior Service',
                'description' => 'The Defense Superior Service Medal (DSSM) is the second highest award bestowed by the Department of Defense. Awarded in the name of the Secretary of Defense, the award is presented to members of the U.S. Armed Forces who perform "superior meritorious service in a position of significant responsibility."  Created on February 6th, 1976 by President Gerald R. Ford\'s Executive Order 11904, it is typically awarded only to senior officers of the Flag and General Officer grade.',
            ],
            [
                'name' => 'Legion of Merit',
                'description' => 'The Legion of Merit Medal (LM, LOM) is a decoration presented by the United States Armed Forces to members of the United States Military, as well as foreign military members and political figures, who have displayed exceptionally meritorious conduct in the performance of outstanding services and achievements. The performance must be of significant importance and far exceed what is expected by normal standards. When the award is presented to foreign parties, it is divided into separate ranking degrees. The degrees are as follows: Chief Commander - issued to a head of state or government; Commander - issued to a chief of staff or higher position that is not head of state; Officer - issued to a general or flag officer that is below the chief of staff, colonel or equivalent rank; Legionnaire - issued to all other service members ranking lower than those previously mentioned. Awards presented to United State military members are not divided into degrees. Subsequent awards are denoted by Oak Leaf Clusters for U.S. Army and Air Force members and Award Stars for U.S. Navy, Marine Corps and Coast Guard members. The Valor device is also authorized to be worn by the U.S. Navy, Marine Corps and Coast Guard, but not by the U.S. Army or Air Force.',
            ],
            [
                'name' => 'Bronze Star',
                'description' => 'The Bronze Star Medal (BSM or BSV) is an award presented to United States Armed Forces personnel for bravery, acts of merit or meritorious service. When awarded for combat heroism it is awarded with a V device for Valor. It is the fourth highest combat award of the Armed Forces.',
            ],
            [
                'name' => 'Purple Heart',
                'description' => 'The Purple Heart Medal (PH) is a decoration presented in the name of the President of the United States to recognize members of the U.S. military who have been wounded or killed in battle. It differs from other military decorations in that a "recommendation" from a superior is not required, but rather individuals are entitled based on meeting certain criteria found in AR 600-8-22. This criteria was expanded on March 28, 1973 to include injuries received as a result of an international terrorist attack against the U.S. and while serving outside the territory of the U.S. as part of a peacekeeping force. Personnel wounded or killed by friendly fire are also eligible for this award as long as the injuries were received in combat and with the intention of inflicting harm on the opposing forces. The Purple Heart is not awarded for non-combat injuries and commanders must take into account the extent of enemy involvement in the wound.',
            ],
            [
                'name' => 'Defense Meritorious Service',
                'description' => 'The Defense Meritorious Service Medal (DMSM) is an award presented in the name of the Secretary of Defense to members of the Armed Forces. It is the third-highest award that the Department of Defense issues, and is awarded to those who distinguish themselves though non-combat meritorious service or achievement, in a joint capacity. Created on November 3rd, 1977 by President Jimmy Carter\'s Executive Order 12019, it was first awarded to Major Terrell G Covington of the United States Army.',
            ],
            [
                'name' => 'Meritorious Service',
                'description' => 'The Meritorious Service Medal (MSM) is a decoration presented by the United States Armed Forces to recognize superior and exceptional non-combat service that does not meet the caliber of the Legion of Merit Medal. As of September 11, 2001, this award may also be issued for outstanding service in specific combat theater. The majority of recipients are field grade officers, senior warrant officers, senior non-commissioned officers and foreign military personnel in the ranks of O-6 and below. Subsequent awards are denoted by bronze oak leafs for Army and Air Force members, and gold stars for Navy, Marine Corps and Coast Guard members.',
            ],
        ];

        foreach ($awards as $award) {
            Award::factory()->state($award)->create();
        }
    }
}
