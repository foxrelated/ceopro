UPDATE `engine4_core_modules` SET `version` = '4.09p1' WHERE `name` = 'advgroup';

-- FIX SOCIAL MUSIC MENUS NAMES
UPDATE `engine4_core_menuitems` SET `name` = 'advgroup_profile_social_music_album' WHERE `name` = 'advgroup_profile_social-music-album';
UPDATE `engine4_core_menuitems` SET `name` = 'advgroup_profile_social_music_song' WHERE `name` = 'advgroup_profile_social-music-song';